<?php

namespace Elanode\ImageProcessor\Facades;

use Illuminate\Support\Facades\Facade;

class ImageSaver extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'imagesaver';
    }
}
