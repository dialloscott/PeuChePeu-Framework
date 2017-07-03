<?php

namespace App\Registration;

use App\Registration\Twig\RegistrationExtension;
use Framework\App;
use Framework\Module;
use Framework\View\TwigView;

class RegistrationModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(App $app)
    {
        $app
            ->map(['GET', 'POST'], '/inscription', [Controller\RegistrationController::class, 'register'])
            ->setName('registration.signup');

        /* @var \Framework\View\ViewInterface */
        $view = $app->getContainer()->get('view');
        $view->addPath(__DIR__ . '/views', 'registration');
        if ($view instanceof TwigView) {
            $view->getTwig()->addExtension(new RegistrationExtension($view));
        }
    }
}
