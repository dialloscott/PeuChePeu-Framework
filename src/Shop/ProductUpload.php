<?php

namespace App\Shop;

use Framework\Upload;

class ProductUpload extends Upload
{
    protected $formats = [
        'thumb' => [318, 180]
    ];
}
