<?php

return [
    /*
    |--------------------------------------------------------------------------
    | QR Code Generator Backend
    |--------------------------------------------------------------------------
    |
    | The backend to use when generating QR codes.
    | Available: 'imagick', 'gd'
    | 
    | GD is recommended as it's included with most PHP installations.
    | Imagick requires the imagick PHP extension to be installed.
    |
    */
    
    'generator' => 'gd',
];
