<?php

namespace App\Contact;

use Core\App;
use Core\Module;
use Core\View\ViewInterface;

class ContactModule extends Module
{
    public function __construct(App $app)
    {
        $app->getContainer()->get(ViewInterface::class)->addPath(__DIR__ . '/views', 'contact');

        // Routes
        $app
            ->map(['GET', 'POST'], '/contact', [Controller\ContactController::class, 'contact'])
            ->setName('contact');
    }
}
