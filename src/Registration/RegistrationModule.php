<?php

namespace App\Registration;

use App\Auth\Middleware\LoggedinMiddleware;
use App\Registration\Controller\AccountController;
use App\Registration\Controller\RegistrationController;
use App\Registration\Twig\RegistrationExtension;
use Framework\App;
use Framework\Module;
use Framework\View\TwigView;
use Framework\View\ViewInterface;

class RegistrationModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(App $app, LoggedinMiddleware $loggedinMiddleware, ViewInterface $view)
    {
        $app
            ->map(['GET', 'POST'], '/inscription', [RegistrationController::class, 'register'])
            ->setName('registration.signup');
        $app
            ->get('/mon-compte', [AccountController::class, 'account'])
            ->setName('registration.account')
            ->add($loggedinMiddleware);
        $app->delete('/mon-compte', [AccountController::class, 'delete'])->add($loggedinMiddleware);

        /* @var \Framework\View\ViewInterface */
        $view->addPath(__DIR__ . '/views', 'registration');
        if ($view instanceof TwigView) {
            $view->getTwig()->addExtension(new RegistrationExtension($view));
        }
    }
}
