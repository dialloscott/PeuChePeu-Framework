<?php

namespace App\Error;

use Core\App;
use Core\Module;
use Core\View\ViewInterface;

class ErrorModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(App $app)
    {
        $app->getContainer()->get(ViewInterface::class)->addPath(__DIR__, 'error');
    }
}
