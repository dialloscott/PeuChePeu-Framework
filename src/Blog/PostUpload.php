<?php

namespace App\Blog;

use Framework\Upload;

class PostUpload extends Upload
{
    /**
     * Liste les formats à générer.
     *
     * @var array
     */
    protected $formats = [
        'thumb' => [318, 180]
    ];
}
