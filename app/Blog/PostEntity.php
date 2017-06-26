<?php

namespace App\Blog;

class PostEntity
{
    public $id;

    public $name;

    public $slug;

    public $content;

    public $created_at;

    public $image;

    public function getThumb () {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
        return '/uploads/posts/' . $filename . '_thumb.' . $extension;
    }
}
