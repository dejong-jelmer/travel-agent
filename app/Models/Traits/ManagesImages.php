<?php

namespace App\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait ManagesImages
{
    /**
     * Sync images - intelligently add new, keep existing, and remove deleted images.
     *
     * Uses DB transaction to ensure atomicity. Upload order:
     * 1. Upload new files first (before transaction)
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
        DB::transaction(function () use ($incomingData, $relation, $isFeatured) {
            // Lock the parent model to prevent concurrent image syncs
            $this->lockForUpdate()->find($this->id);
            // Get existing images from database
            $existingImages = $this->$relation()->lockForUpdate()->get();
            $existingPaths = $existingImages->pluck('path')->toArray();

            // Separate incoming data into existing paths and new uploads
            $incomingPaths = [];
            $newUploads = [];

            foreach ($incomingData as $item) {
                if (is_string($item)) {
                    $incomingPaths[] = $item;
                } elseif ($item instanceof UploadedFile) {
                    $newUploads[] = $item;
                }
            }

            // Determine images to delete (in DB but not in incoming data)
            $diffPaths = array_diff($existingPaths, $incomingPaths);
            $pathsToDelete = Arr::map($diffPaths, fn (string $value, string $key) => basename($value));

            // Upload new files
            // Track uploaded files for cleanup on failure
            $uploadedFiles = [];

            try {
                foreach ($newUploads as $upload) {
                    $fullPath = $upload->store(config('images.disk'), config('images.directory'));

                    if ($fullPath) {
                        // Extract hash filename only for database
                        $hashFilename = basename($fullPath);

                        $uploadedFiles[] = [
                            'path' => $hashFilename,
                            'original_name' => $upload->getClientOriginalName(),
                            'featured' => $isFeatured,
                            'mime_type' => $upload->getClientMimeType(),
                            'size' => $upload->getSize(),
                        ];
                    }
                }

                foreach ($pathsToDelete as $pathToDelete) {
                    $imageToDelete = $existingImages->firstWhere('path', $pathToDelete);
                    if ($imageToDelete) {
                        $imageToDelete->forceDelete();
                    }
                }

                // Create new database records for uploaded files
                foreach ($uploadedFiles as $file) {
                    $this->$relation()->create([
                        'path' => $file['path'],
                        'original_name' => $file['original_name'],
                        'featured' => $file['featured'],
                        'mime_type' => $file['mime_type'],
                        'size' => $file['size'],
                    ]);
                }

                // Delete old storage files
                foreach ($pathsToDelete as $pathToDelete) {
                    Storage::disk(config('images.directory'))->delete(config('images.disk').'/'.$pathToDelete);
                }
            } catch (\Exception $e) {
                // Step 4: Cleanup uploaded files on failure
                foreach ($uploadedFiles as $file) {
                    // $file is array with 'path' key containing hash filename
                    Storage::disk(config('images.directory'))->delete(config('images.disk').'/'.$file['path']);
                }
                // Log & Re-throw exception for proper error handling
                Log::error('Image sync failed', [
                    'model' => get_class($this),
                    'id' => $this->id,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }
}
