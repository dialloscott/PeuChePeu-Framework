<?php

namespace App\Auth;

use App\Auth\Controller\PasswordController;
use App\Auth\Controller\SessionController;
use App\Auth\Middleware\LoggedinMiddleware;
use Framework\App;
use Framework\Module;
use Framework\View\ViewInterface;

class AuthModule extends Module
{
    public const MIGRATIONS = __DIR__ . '/db/migrations';
    public const SEEDS = __DIR__ . '/db/seeds';
    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(ViewInterface $view, AuthService $authService, App $app)
    {
        // Gestion des views
        $view->addPath(__DIR__ . '/views', 'auth');

        // Gestion des routes
        $app->get('/login', [SessionController::class, 'create'])->setName('auth.login');
        $app->post('/login', [SessionController::class, 'store']);
        $app
            ->delete('/logout', [SessionController::class, 'destroy'])
            ->setName('auth.logout')
            ->add(new LoggedinMiddleware($authService));

        $app->get('/password/reset', [PasswordController::class, 'formReset'])->setName('auth.password_reset');
        $app->post('/password/reset', [PasswordController::class, 'reset']);
        $app->get('/password/recover/{id}/{token}', [PasswordController::class, 'recover'])
            ->setName('auth.password_recover');
        $app->post('/password/recover/{id}/{token}', [PasswordController::class, 'recover']);
    }
}
