<?php

use Intervention\Image\Drivers\Gd\Driver;

return [

    /*
    |--------------------------------------------------------------------------
    | Image Processing Driver
    |--------------------------------------------------------------------------
    |
    | The Intervention Image driver used to generate optimized image
    | variants. Supported out of the box: the GD driver and the Imagick
    | driver (\Intervention\Image\Drivers\Imagick\Driver).
    |
    */

    'driver' => env('IMAGE_DRIVER', Driver::class),

    /*
    |--------------------------------------------------------------------------
    | Disks
    |--------------------------------------------------------------------------
    |
    | The original, unprocessed upload is kept on the private disk and is
    | never served publicly. Optimized thumbnail and large variants are
    | stored on the public disk, which is what the application displays.
    |
    */

    'original_disk' => 'local',

    'public_disk' => 'public',

    /*
    |--------------------------------------------------------------------------
    | Variants
    |--------------------------------------------------------------------------
    |
    | The maximum width (images are only ever scaled down, never up) and the
    | encoding quality used for each generated variant.
    |
    */

    'thumbnail' => [
        'width' => 480,
        'quality' => 75,
    ],

    'large' => [
        'width' => 1920,
        'quality' => 82,
    ],

];
