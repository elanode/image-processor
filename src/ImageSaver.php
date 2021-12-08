<?php

namespace Elanode\ImageProcessor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageSaver
{
    public function uploadThumbnails(Request $request, string $folderName, string $requestArrayField, string $field)
    {
        $result = [];
        $requestImages = $request->file($requestArrayField);

        if ($requestImages) {
            foreach ($requestImages as $index => $val) {
                $thumbPath = $this->saveThumbnailImage($requestImages[$index][$field], $folderName);

                array_push($result, $thumbPath);
            }
        }

        return $result;
    }

    public function uploadThumbnail(Request $request, string $folderName, string $field, $width = null, $height = 300): string
    {
        $image = $request->file($field);

        $thumbPath = $this->saveThumbnailImage($image, $folderName, $width, $height);

        return $thumbPath;
    }

    public function uploadOriginal(Request $request, string $folderName, string $field)
    {
        $image = $request->file($field);

        $oriPath = $this->saveOriginalImage($image, $folderName);

        return $oriPath;
    }

    /**
     * Upload multiple images to specified folder
     * 
     */
    public function uploadImages(Request $request, string $folderName, string $requestArrayField)
    {
        $result = [];
        $requestImages = $request->file($requestArrayField);

        if ($requestImages) {
            foreach ($requestImages as $index => $val) {
                $originalImage = $requestImages[$index];
                $originalPath = $this->saveOriginalImage($originalImage, $folderName);
                $thumbPath = $this->saveThumbnailImage($originalImage, $folderName);

                array_push($result, [
                    'original_image' => $originalPath,
                    'thumbnail_image' => $thumbPath
                ]);
            }
        }

        return $result;
    }

    public function saveOriginalImage($originalImage, $folderName)
    {
        $newImage = Image::make($originalImage)->orientate();
        $path = $this->generatePath($originalImage, $folderName);
        $newImage->encode(
            config('imagesaver.defaults.original.ext'),
            config('imagesaver.defaults.original.quality')
        );

        Storage::disk('public')->put($path, $newImage);
        return $path;
    }

    public function saveThumbnailImage($originalImage, $folderName, $width = null, $height = null)
    {
        if ($height === null && $width === null) {
            $height = config('imagesaver.defaults.thumbnail.height');
        }

        $newImage = Image::make($originalImage)->orientate();
        $path = $this->generatePath($originalImage, $folderName, true);
        $newImage->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $newImage->encode(
            config('imagesaver.defaults.thumbnail.ext'),
            config('imagesaver.defaults.thumbnail.quality')
        );

        Storage::disk('public')->put($path, $newImage);
        return $path;
    }


    public function generatePath($originalImage, $folderName, $thumbnail = false): string
    {
        $thumbInfo = $thumbnail ? 'thumbnail_' : '';
        $filename = time() . "_" . $thumbInfo . preg_replace('/\s+/', '_', strtolower($originalImage->getClientOriginalName()));
        return $filename ? $folderName . '/' . $filename : null;
    }
}
