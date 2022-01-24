<?php

namespace Elanode\ImageProcessor;

use Exception;
use Intervention\Image\Image;
use Intervention\Image\Facades\Image as ImageFacade;
use Throwable;

class ImageMaker
{
    public function __construct(
        protected string $encoding = 'jpg',
        protected bool $maintainAspect = true,
        protected string $fileNamePostFix = 'default',
        protected int $quality = 90,
        protected string $formattedFilename = ''
    ) {
    }

    /**
     * Set encoding
     * 
     * @param string $encoding
     * @return $this
     */
    public function setEncoding(string $encoding): self
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Set maintain aspect ratio
     * 
     * @param bool $maintainAspect
     * @return $this
     */
    public function setMaintainAspect(bool $maintainAspect): self
    {
        $this->maintainAspect = $maintainAspect;
        return $this;
    }

    /**
     * Set post fix filename.
     * e.g. image_[w800].jpg
     * 
     * @param string $fileNamePostFix
     * @return $this
     */
    public function setFileNamePostFix(bool $fileNamePostFix): self
    {
        $this->fileNamePostFix = $fileNamePostFix;
        return $this;
    }

    /**
     * Set quality.
     * 
     * @param int $quality
     * @return $this
     */
    public function setQuality(bool $quality): self
    {
        $this->quality = $quality;
        return $this;
    }

    /**
     * Get last formatted filename.
     * 
     * @return string
     */
    public function getLatestFormattedFilename(): string
    {
        return $this->formattedFilename;
    }

    /**
     * Generate the image
     * 
     * @param mixed $image
     * @param int|null $width
     * @param int|null $height
     * @return Image
     * 
     * @throws Throwable
     */
    public function generateImage($image,  $width,  $height): Image
    {
        if ($width == null && $height == null) {
            throw new \Exception('width or height should be filled');
        }

        $new = ImageFacade::make($image)->orientate();
        $maintain = $this->maintainAspect;

        $new->resize($width, $height, function ($constraint) use ($maintain) {
            if ($maintain) {
                $constraint->aspectRatio();
            }
        })->encode($this->encoding, $this->quality);

        $this->formattedFilename = $this->formatFilename($image);

        return $new;
    }

    private function formatFilename($originalImage): string
    {
        $filename =  time() . "_" . preg_replace('/\s+/', '_', strtolower($originalImage->getClientOriginalName())) . '-' . $this->fileNamePostFix;
        return $filename;
    }
}
