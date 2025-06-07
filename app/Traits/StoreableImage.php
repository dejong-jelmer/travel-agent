<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
// Optioneel, voor logging van fouten
use InvalidArgumentException;

trait StoreableImage
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
}
