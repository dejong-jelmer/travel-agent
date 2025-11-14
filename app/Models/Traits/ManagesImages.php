<?php

namespace App\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

trait ManagesImages
{
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
            $originalFilename = $image->getClientOriginalName();
            $storedPath = $image->storeAs(
                self::IMAGE_DIRECTORY,
                $originalFilename,
                'public'
            );

            if (!$storedPath) {
                throw new \RuntimeException("Failed to store image: {$originalFilename}");
            }

            $imageDatabaseEntries[] = [
                'path' => $originalFilename,
                'featured' => $isFeatured,
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
                $originalFilename = $upload->getClientOriginalName();
                $storedPath = $upload->storeAs(
                    self::IMAGE_DIRECTORY,
                    $originalFilename,
                    'public'
                );

                if ($storedPath) {
                    $uploadedFiles[] = $originalFilename;
                }
            }

            // Step 2: DB transaction for atomic database operations
            DB::transaction(function () use ($relation, $existingImages, $pathsToDelete, $uploadedFiles, $isFeatured) {
                // Delete old database records (files deleted after commit)
                foreach ($pathsToDelete as $pathToDelete) {
                    $imageToDelete = $existingImages->firstWhere('path', $pathToDelete);
                    if ($imageToDelete) {
                        $imageToDelete->forceDelete();
                    }
                }

                // Create new database records for uploaded files
                foreach ($uploadedFiles as $filename) {
                    $this->$relation()->create([
                        'path' => $filename,
                        'featured' => $isFeatured,
                    ]);
                }
            });

            // Step 3: After successful commit, delete old storage files
            foreach ($pathsToDelete as $pathToDelete) {
                Storage::disk('public')->delete(self::IMAGE_DIRECTORY.'/'.basename($pathToDelete));
            }

        } catch (\Exception $e) {
            // Step 4: Cleanup uploaded files on failure
            foreach ($uploadedFiles as $filename) {
                Storage::disk('public')->delete(self::IMAGE_DIRECTORY.'/'.$filename);
            }

            // Re-throw exception for proper error handling
            throw $e;
        }

        // Existing images (paths in $incomingPaths) are automatically kept - no action needed
    }
}
