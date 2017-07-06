<?php

namespace App\Shop\Controller;

use App\Shop\Table\ProductTable;
use Framework\Controller;

class ProductController extends Controller
{
    public function index(ProductTable $productTable)
    {
        $products = $productTable->findAll();

        return $this->render('@shop/index', compact('products'));
    }
}
