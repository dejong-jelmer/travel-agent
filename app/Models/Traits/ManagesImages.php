<?php

namespace App\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

trait ManagesImages
{
    private const STORAGE_DISK = 'public';

    private const IMAGE_DIRECTORY = 'images';

    /**
     * Store uploaded images.
     *
     * @param  UploadedFile|array<int, UploadedFile>  $files  The file(s) to store.
     * @param  string  $relation  The name of the Eloquent relation.
     * @param  bool  $isFeatured  Whether the image(s) should be marked as featured.
     *
     * @throws InvalidArgumentException if a file is not an instance of UploadedFile.
     * @throws RuntimeException if failed to store file.
     */
    public function storeImages(UploadedFile|array $files, string $relation, bool $isFeatured = false): void
    {
        $imageDatabaseEntries = [];
        $imagesToProcess = is_array($files) ? $files : [$files];
        foreach ($imagesToProcess as $image) {
            if (! $image instanceof UploadedFile) {
                throw new InvalidArgumentException('Expected instance of Illuminate\Http\UploadedFile.');
            }
            // Store with Laravel hash in public/images directory
            $fullPath = $image->store(self::IMAGE_DIRECTORY, self::STORAGE_DISK);

            if (! $fullPath) {
                throw new \RuntimeException("Failed to store image: {$image->getClientOriginalName()}");
            }

            // Extract hash filename only (without "images/" prefix) for database
            $hashFilename = basename($fullPath);

            $imageDatabaseEntries[] = [
                'path' => $hashFilename,
                'original_name' => $image->getClientOriginalName(),
                'featured' => $isFeatured,
                'mime_type' => $image->getClientMimeType(),
                'size' => $image->getSize(),
            ];
        }

        $this->$relation()->forceDelete();

        if (! empty($imageDatabaseEntries)) {
            $this->$relation()->createMany($imageDatabaseEntries);
        }
    }

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

        // Get existing images from database
        $existingImages = $this->$relation()->get();
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
        $pathsToDelete = array_diff($existingPaths, $incomingPaths);

        // Step 1: Upload new files FIRST (before transaction)
        // Track uploaded files for cleanup on failure
        $uploadedFiles = [];

        try {
            foreach ($newUploads as $upload) {
                // Store with Laravel hash in public/images directory
                $fullPath = $upload->store(self::IMAGE_DIRECTORY, self::STORAGE_DISK);

                if ($fullPath) {
                    // Extract hash filename only (without "images/" prefix) for database
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

            // Step 2: DB transaction for atomic database operations
            DB::transaction(function () use ($relation, $existingImages, $pathsToDelete, $uploadedFiles) {
                // Delete old database records (files deleted after commit)
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
            });

            // Step 3: After successful commit, delete old storage files
            foreach ($pathsToDelete as $pathToDelete) {
                // $pathToDelete is hash filename only (e.g., "Ab3Cd5Ef7.jpg")
                Storage::disk(self::STORAGE_DISK)->delete(self::IMAGE_DIRECTORY.'/'.$pathToDelete);
            }
        } catch (\Exception $e) {
            // Step 4: Cleanup uploaded files on failure
            foreach ($uploadedFiles as $file) {
                // $file is array with 'path' key containing hash filename
                Storage::disk(self::STORAGE_DISK)->delete(self::IMAGE_DIRECTORY.'/'.$file['path']);
            }

            // Re-throw exception for proper error handling
            throw $e;
        }

        // Existing images (paths in $incomingPaths) are automatically kept - no action needed
    }
}
