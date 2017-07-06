<?php

namespace App\Shop;

use App\Shop\Controller\AdminProductController;
use App\Shop\Controller\ProductController;
use App\Shop\Widget\ProductWidget;
use Framework\App;
use Framework\Module;
use Framework\View\ViewInterface;

class ShopModule extends Module
{
    public const MIGRATIONS = __DIR__ . '/db';

    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(App $app)
    {
        $container = $app->getContainer();

        // Views
        $container->get(ViewInterface::class)->addPath(__DIR__ . '/views', 'shop');

        // Routes
        $app->get('/boutique', [ProductController::class, 'index'])->setName('shop.index');

        // Pour le backend
        if ($container->has('admin.middleware')) {
            $app->group($container->get('admin.prefix'), function () {
                // Gestion des articles
                $this->get('/products', [AdminProductController::class, 'index'])->setName('shop.admin.index');
                $this
                    ->map(['GET', 'POST'], '/products/new', [AdminProductController::class, 'create'])
                    ->setName('shop.admin.create');
                $this
                    ->map(['GET', 'PUT'], '/products/{id:[0-9]+}', [AdminProductController::class, 'edit'])
                    ->setName('shop.admin.edit');
                $this
                    ->delete('/products/{id:[0-9]+}', [AdminProductController::class, 'destroy'])
                    ->setName('shop.admin.destroy');
            })->add($container->get('admin.middleware'));
        }

        // Gestion du widget
        if ($container->has('admin.widgets')) {
            $container->get('admin.widgets')->add($container->get(ProductWidget::class));
        }
    }
}
