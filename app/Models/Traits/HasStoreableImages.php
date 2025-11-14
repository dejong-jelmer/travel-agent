<?php

namespace App\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

trait HasStoreableImages
{
    /**
     * Store uploaded images.
     *
     * @param  UploadedFile|array<int, UploadedFile>  $files  The file(s) to store.
     * @param  string  $relation  The name of the Eloquent relation.
     * @param  bool  $isFeatured  Whether the image(s) should be marked as featured.
     *
     * @throws InvalidArgumentException if a file is not an instance of UploadedFile.
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
                'images',
                $originalFilename,
                'public'
            );
            if ($storedPath) {
                $imageDatabaseEntries[] = [
                    'path' => $originalFilename,
                    'featured' => $isFeatured,
                ];
            }
        }
        $this->$relation()->forceDelete();
        if (! empty($imageDatabaseEntries)) {
            $this->$relation()->createMany($imageDatabaseEntries);
        }
    }

    /**
     * Sync images - intelligently add new, keep existing, and remove deleted images.
     *
     * @param  string|UploadedFile|array<int, string|UploadedFile>  $data  Mixed array of paths (strings) and new uploads (UploadedFile).
     * @param  string  $relation  The name of the Eloquent relation.
     * @param  bool  $isFeatured  Whether the image(s) should be marked as featured.
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

        // Delete removed images from database and storage
        foreach ($pathsToDelete as $pathToDelete) {
            $imageToDelete = $existingImages->firstWhere('path', $pathToDelete);
            if ($imageToDelete) {
                // Delete from storage
                Storage::disk('public')->delete('images/'.$pathToDelete);
                // Delete from database
                $imageToDelete->forceDelete();
            }
        }

        // Upload and store new images
        foreach ($newUploads as $upload) {
            $originalFilename = $upload->getClientOriginalName();
            $storedPath = $upload->storeAs(
                'images',
                $originalFilename,
                'public'
            );

            if ($storedPath) {
                $this->$relation()->create([
                    'path' => $originalFilename,
                    'featured' => $isFeatured,
                ]);
            }
        }

        // Existing images (paths in $incomingPaths) are automatically kept - no action needed
    }
}
