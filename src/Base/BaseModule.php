<?php

namespace App\Base;

use App\Base\Controller\HomeController;
use Framework\App;
use Framework\Module;
use Framework\View\ViewInterface;

class BaseModule extends Module
{
    public function __construct(App $app, ViewInterface $view)
    {
        $view->addPath(__DIR__ . '/views');
        $app->get('/', [HomeController::class, 'index'])->setName('root');
    }
}
