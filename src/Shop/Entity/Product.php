<?php

namespace App\Shop\Entity;

class Product
{
    public $name;

    public $description;

    public $created_at;

    public $image;

    public function getThumb()
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);

        return '/uploads/produits/' . $filename . '_thumb.' . $extension;
    }
}
