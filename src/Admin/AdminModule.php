<?php

namespace App\Admin;

use App\Auth\Middleware\RoleMiddleware;
use Framework\App;
use Framework\Module;
use Framework\View\ViewInterface;

class AdminModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(App $app, ViewInterface $view, string $prefix, RoleMiddleware $roleMiddleware)
    {
        // Gestion des vues
        $view->addPath(__DIR__ . '/views', 'admin');

        // Gestion des routes
        $app->group($app->getContainer()->get('admin.prefix'), function () {
            $this->get('', [AdminController::class, 'index'])->setName('admin.index');
        })->add($roleMiddleware);
    }
}
