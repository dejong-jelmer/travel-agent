<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

trait StoreableImage
{
    /**
     * Store uploaded images.
     *
     * @param  $files  \Illuminate\Http\UploadedFile|array<int, \Illuminate\Http\UploadedFile>
     * @param  $relation  String
     * @param  $isFeatured  bool
     *
     * @throws \InvalidArgumentException if array doesn't have instances of \Illuminate\Http\UploadedFile
     */
    public function storeImages(UploadedFile|array $files, string $relation, bool $isFeatured = false): void
    {
        $imagePaths = [];
        $images = is_array($files) ? $files : [$files];

        foreach ($images as $image) {
            if (! $image instanceof UploadedFile) {
                throw new InvalidArgumentException('Expected instance of Illuminate\Http\UploadedFile.');
            }
            $imagePaths[] = [
                'path' => $image->storeAs('', $image->getClientOriginalName(), 'public') ?: $image->getClientOriginalName(),
                'featured' => $isFeatured,
            ];
        }

        $this->$relation()->forceDelete();
        $this->$relation()->createMany($imagePaths);
    }
}
