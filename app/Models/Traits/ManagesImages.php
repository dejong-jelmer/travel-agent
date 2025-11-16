<?php

namespace App\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait ManagesImages
{
    /**
     * Sync images - add new, keep existing, and remove deleted images.
     *
     * Uses two-phase commit to ensure atomicity and avoid race conditions:
     * 1. Upload new files first (outside transaction)
     * 2. DB transaction: delete old records, create new records
     * 3. After commit: delete old storage files
     * 4. On failure: cleanup new uploads and rollback
     *
     * @param  string|UploadedFile|array<int, string|UploadedFile>  $data  Mixed array of paths (strings) and new uploads (UploadedFile).
     * @param  string  $relation  The name of the Eloquent relation.
     * @param  bool  $isFeatured  Whether the image(s) should be marked as featured.
     *
     * @throws \Exception If any operation fails
     */
    public function syncImages(string|UploadedFile|array $data, string $relation, bool $isFeatured = false): void
    {
        $incomingData = is_array($data) ? $data : [$data];

        // Separate incoming data into existing paths and new uploads
        $incomingPaths = [];
        $newUploads = [];

        foreach ($incomingData as $item) {
            if (is_string($item)) {
                $incomingPaths[] = basename($item);
            } elseif ($item instanceof UploadedFile) {
                $newUploads[] = $item;
            }
        }

        // PHASE 1: Upload new files (outside transaction to avoid holding locks during I/O)
        $uploadedFiles = $this->uploadNewImages($newUploads, $isFeatured);

        try {
            // PHASE 2: Update database records in transaction
            $storagePathsToDelete = $this->updateImageRecordsInTransaction($incomingPaths, $uploadedFiles, $relation);

            // PHASE 3: Delete old storage files after successful transaction
            $this->deleteStorageFiles($storagePathsToDelete);
        } catch (\Exception $e) {
            // Cleanup: Delete newly uploaded files if anything failed
            $this->deleteStorageFiles(array_column($uploadedFiles, 'path'));

            Log::error('Image sync failed', [
                'model' => get_class($this),
                'id' => $this->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Upload new image files to storage.
     *
     * @param  array<int, UploadedFile>  $uploads  Array of uploaded files.
     * @param  bool  $isFeatured  Whether images should be marked as featured.
     * @return array<int, array{path: string, original_name: string, featured: bool, mime_type: string, size: int}>
     */
    private function uploadNewImages(array $uploads, bool $isFeatured): array
    {
        $uploadedFiles = [];

        foreach ($uploads as $upload) {
            $fullPath = $upload->store(config('images.directory'), config('images.disk'));

            if ($fullPath) {
                $uploadedFiles[] = [
                    'path' => basename($fullPath),
                    'original_name' => $upload->getClientOriginalName(),
                    'featured' => $isFeatured,
                    'mime_type' => $upload->getClientMimeType(),
                    'size' => $upload->getSize(),
                ];
            }
        }

        return $uploadedFiles;
    }

    /**
     * Update image records in database transaction.
     *
     * Removes old records, creates new records, and returns paths of deleted images.
     *
     * @param  array<int, string>  $incomingPaths  Array of existing image paths to keep.
     * @param  array<int, array>  $uploadedFiles  Array of newly uploaded file data.
     * @param  string  $relation  The name of the Eloquent relation.
     * @return array<int, string> Array of storage paths that should be deleted.
     */
    private function updateImageRecordsInTransaction(array $incomingPaths, array $uploadedFiles, string $relation): array
    {
        $storagePathsToDelete = [];

        DB::transaction(function () use ($incomingPaths, $uploadedFiles, $relation, &$storagePathsToDelete) {
            $this->lockForUpdate();

            $existingImages = $this->$relation()->lockForUpdate()->get();
            $existingPaths = $existingImages->pluck('raw_path')->toArray();

            $pathsToDelete = array_diff($existingPaths, $incomingPaths);

            if (! empty($pathsToDelete)) {
                $this->$relation()->whereIn('path', $pathsToDelete)->forceDelete();
            }

            $storagePathsToDelete = $pathsToDelete;

            foreach ($uploadedFiles as $file) {
                $this->$relation()->create([
                    'path' => $file['path'],
                    'original_name' => $file['original_name'],
                    'featured' => $file['featured'],
                    'mime_type' => $file['mime_type'],
                    'size' => $file['size'],
                ]);
            }
        });

        return $storagePathsToDelete;
    }

    /**
     * Delete image files from storage.
     *
     * @param  array<int, string>  $paths  Array of image paths to delete.
     */
    private function deleteStorageFiles(array $paths): void
    {
        foreach ($paths as $path) {
            Storage::disk(config('images.disk'))->delete(config('images.directory').'/'.$path);
        }
    }
}
