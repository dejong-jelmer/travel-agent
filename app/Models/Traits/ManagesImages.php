<?php

namespace App\Models\Traits;

use App\Enums\ImageRelation;
use App\Models\Image;
use BadMethodCallException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

trait ManagesImages
{
    /**
     * Boot the trait and register a deleting event to clean up associated images.
     *
     * For models with SoftDeletes, images are only purged on forceDelete.
     * For models without SoftDeletes, images are purged on every delete.
     */
    public static function bootManagesImages(): void
    {
        static::deleting(function ($model) {
            if (! method_exists($model, 'isForceDeleting') || $model->isForceDeleting()) {
                $model->purgeImages();
            }
        });
    }

    /**
     * Delete all associated images from storage and database.
     */
    public function purgeImages(): void
    {
        $images = $this->morphMany(Image::class, 'imageable')->withTrashed()->get();

        $paths = $images->pluck('path')->toArray();

        $this->deleteStorageFiles($paths);

        $this->morphMany(Image::class, 'imageable')->withTrashed()->forceDelete();
    }

    /**
     * Handle an image field from a request: upload, keep existing, or delete.
     *
     * @param  Request  $request  The current HTTP request.
     * @param  string  $field  The request field name (e.g. 'featured_image').
     * @param  ImageRelation  $relation  The image relation enum.
     * @param  bool  $isPrimary  Whether the image should be marked as primary.
     */
    public function syncImageFromRequest(Request $request, string $field, ImageRelation $relation, bool $isPrimary = false): void
    {
        if ($request->hasFile($field)) {
            $this->syncImages($request->file($field), $relation, $isPrimary);
        } elseif ($request->has($field) && is_null($request->input($field))) {
            $relation->getRelation($this)->first()?->delete();
        } elseif ($request->filled($field)) {
            $this->syncImages($request->input($field), $relation, $isPrimary);
        }
    }

    /**
     * Sync images - add new, keep existing, and remove deleted images.
     *
     * Uses two-phase commit to ensure atomicity and avoid race conditions:
     * 1. Upload new files first (outside transaction)
     * 2. DB transaction: delete old records, create new records
     * 3. After commit: delete old storage files
     * 4. On failure: cleanup new uploads and rollback
     *
     * The position of each item in $data is stored as the display order.
     *
     * @param  string|UploadedFile|array<int, string|UploadedFile>  $data  Mixed array of paths (strings) and new uploads (UploadedFile).
     * @param  ImageRelation  $relation  Instance of enum with the name of the Eloquent relation.
     * @param  bool  $isPrimary  Whether the image(s) should be marked as is_primary.
     *
     * @throws Exception If any operation fails
     */
    public function syncImages(string|UploadedFile|array $data, ImageRelation $relation, bool $isPrimary = false): void
    {
        // Files and text inputs are merged separately by the request, so sort by key
        // first to restore the submitted order before using positions as display order.
        $incomingData = is_array($data) ? $data : [$data];
        ksort($incomingData, SORT_NUMERIC);
        $incomingData = array_values($incomingData);

        // Separate incoming data into existing paths and new uploads, keeping their position as order
        $incomingPaths = []; // path => order
        $newUploads = []; // [order => UploadedFile]

        foreach ($incomingData as $order => $item) {
            if (is_string($item)) {
                $incomingPaths[basename($item)] = $order;
            } elseif ($item instanceof UploadedFile) {
                $newUploads[$order] = $item;
            }
        }

        // PHASE 1: Upload new files (outside transaction to avoid holding locks during I/O)
        $uploadedFiles = $this->uploadNewImages($newUploads, $isPrimary);

        try {
            // PHASE 2: Update database records in transaction
            $storagePathsToDelete = $this->updateImageRecordsInTransaction($incomingPaths, $uploadedFiles, $relation);

            // PHASE 3: Delete old storage files after successful transaction
            $this->deleteStorageFiles($storagePathsToDelete);
        } catch (\Exception $e) {
            try {
                Log::error('Image sync failed', [
                    'model' => get_class($this),
                    'id' => $this->id,
                    'error' => $e->getMessage(),
                ]);
                // Cleanup: Delete newly uploaded files if anything failed
                $this->deleteStorageFiles(array_column($uploadedFiles, 'path'));
            } catch (\Exception $cleanupException) {
                Log::error('Failed to cleanup uploaded files', [
                    'original_error' => $e->getMessage(),
                    'cleanup_error' => $cleanupException->getMessage(),
                ]);
            }
            // Throw original error
            throw $e;
        }
    }

    /**
     * Upload new image files to storage.
     *
     * @param  array<int, UploadedFile>  $uploads  Uploaded files keyed by their display order.
     * @param  bool  $isPrimary  Whether images should be marked as is_primary.
     * @return array<int, array{path: string, original_name: string, is_primary: bool, mime_type: string, size: int, order: int}>
     *
     * @throws RuntimeException If any operation fails
     */
    private function uploadNewImages(array $uploads, bool $isPrimary): array
    {
        $uploadedFiles = [];

        foreach ($uploads as $order => $upload) {
            $dimensions = $this->readDimensions($upload);

            $fullPath = $upload->store(config('images.directory'), config('images.disk'));

            if (! $fullPath) {
                throw new RuntimeException("Failed to upload image: {$upload->getClientOriginalName()}");
            }

            $uploadedFiles[] = [
                'path' => basename($fullPath),
                'original_name' => $upload->getClientOriginalName(),
                'is_primary' => $isPrimary,
                'mime_type' => $upload->getClientMimeType(),
                'size' => $upload->getSize() ?: 0,
                'width' => $dimensions['width'],
                'height' => $dimensions['height'],
                'order' => $order,
            ];
        }

        return $uploadedFiles;
    }

    /**
     * Update image records in database transaction.
     *
     * Removes old records, updates the order of kept records, creates new records,
     * and returns paths of deleted images.
     *
     * @param  array<string, int>  $incomingPaths  Existing image paths to keep, mapped to their display order.
     * @param  array<int, array>  $uploadedFiles  Array of newly uploaded file data.
     * @param  ImageRelation  $relation  Instance of enum with the name of the Eloquent relation.
     * @return array<int, string> Array of storage paths that should be deleted.
     *
     * @throws BadMethodCallException If the images relation doesn't exists on the model.
     */
    private function updateImageRecordsInTransaction(array $incomingPaths, array $uploadedFiles, ImageRelation $relation): array
    {
        $storagePathsToDelete = [];

        DB::transaction(function () use ($incomingPaths, $uploadedFiles, $relation, &$storagePathsToDelete) {

            $model = $relation->getRelation($this);
            $existingPaths = $model->lockForUpdate()->pluck('path')->toArray();
            $pathsToDelete = array_diff($existingPaths, array_keys($incomingPaths));

            if (! empty($pathsToDelete)) {
                $model->whereIn('path', $pathsToDelete)->forceDelete();
            }

            $storagePathsToDelete = $pathsToDelete;

            foreach ($incomingPaths as $path => $order) {
                $relation->getRelation($this)->where('path', $path)->update(['order' => $order]);
            }

            foreach ($uploadedFiles as $file) {
                $model->create([
                    'path' => $file['path'],
                    'original_name' => $file['original_name'],
                    'is_primary' => $file['is_primary'],
                    'mime_type' => $file['mime_type'],
                    'size' => $file['size'],
                    'width' => $file['width'],
                    'height' => $file['height'],
                    'order' => $file['order'],
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

    /**
     * Read the pixel dimensions of an uploaded image.
     *
     * Returns null values when the file cannot be read as an image, so callers can
     * store the record without width and height rather than failing the upload.
     *
     * @return array{width: int|null, height: int|null}
     */
    private function readDimensions(UploadedFile $upload): array
    {
        $path = $upload->getRealPath();

        if ($path === false) {
            return ['width' => null, 'height' => null];
        }

        $dimensions = @getimagesize($path);

        if ($dimensions === false) {
            return ['width' => null, 'height' => null];
        }

        return [
            'width' => $dimensions[0],
            'height' => $dimensions[1],
        ];
    }
}
