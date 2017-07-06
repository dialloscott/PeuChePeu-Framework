<?php

namespace App\Shop\Widget;

use App\Admin\AdminWidgetInterface;
use App\Shop\Table\ProductTable;
use Framework\View\ViewInterface;

class ProductWidget implements AdminWidgetInterface
{
    /**
     * @var ProductTable
     */
    private $productTable;

    /**
     * @var ViewInterface
     */
    private $view;

    public function __construct(ViewInterface $view, ProductTable $productTable)
    {
        $this->productTable = $productTable;
        $this->view = $view;
    }

    public function render(): string
    {
        return $this->view->render('@shop/widget', [
            'count' => $this->productTable->count()
        ]);
    }
}
