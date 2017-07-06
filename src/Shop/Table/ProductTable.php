<?php

namespace App\Shop\Table;

use App\Shop\Entity\Product;
use Framework\Database\Table;

class ProductTable extends Table
{
    public const TABLE = 'products';

    public const ENTITY = Product::class;
}
