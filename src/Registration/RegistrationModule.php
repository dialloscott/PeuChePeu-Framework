<?php

namespace App\Registration;

use App\Auth\Middleware\LoggedinMiddleware;
use App\Registration\Controller\AccountController;
use App\Registration\Controller\RegistrationController;
use App\Registration\Twig\RegistrationExtension;
use Framework\App;
use Framework\Module;
use Framework\View\TwigView;

class RegistrationModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(App $app)
    {
        $authMiddleware = new LoggedinMiddleware($app->getContainer()->get('auth.service'));

        $app
            ->map(['GET', 'POST'], '/inscription', [RegistrationController::class, 'register'])
            ->setName('registration.signup');
        $app
            ->get('/mon-compte', [AccountController::class, 'account'])
            ->setName('registration.account')
            ->add($authMiddleware);
        $app->delete('/mon-compte', [AccountController::class, 'delete'])->add($authMiddleware);

        /* @var \Framework\View\ViewInterface */
        $view = $app->getContainer()->get('view');
        $view->addPath(__DIR__ . '/views', 'registration');
        if ($view instanceof TwigView) {
            $view->getTwig()->addExtension(new RegistrationExtension($view));
        }
    }
}
