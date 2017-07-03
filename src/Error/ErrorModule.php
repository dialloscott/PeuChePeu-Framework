<?php

namespace App\Error;

use Framework\Module;
use Framework\View\ViewInterface;

class ErrorModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(ViewInterface $view)
    {
        $view->addPath(__DIR__, 'error');
    }
}
