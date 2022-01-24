<?php

namespace Elanode\ImageProcessor\Facades;

use Illuminate\Support\Facades\Facade;

class ImageMaker extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'imagemaker';
    }
}
