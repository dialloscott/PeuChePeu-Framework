<?php

namespace App\Auth;

use App\Auth\Controller\PasswordController;
use App\Auth\Controller\SessionController;
use DI\Container;
use Framework\App;
use Framework\Module;
use Framework\View\TwigView;
use Framework\View\ViewInterface;

class AuthModule extends Module
{
    public const MIGRATIONS = __DIR__ . '/db/migrations';
    public const SEEDS = __DIR__ . '/db/seeds';
    public const DEFINITIONS = __DIR__ . '/config.php';

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $container->call([$this, 'routes']);
        $container->call([$this, 'view']);
        $this->container = $container;
    }

    public function view(ViewInterface $view, Twig\AuthTwigExtension $extension)
    {
        $view->addPath(__DIR__ . '/views', 'auth');
        if ($view instanceof TwigView) {
            $view->getTwig()->addExtension($extension);
        }
    }

    public function routes(App $router)
    {
        $router->get('/login', [SessionController::class, 'create'])->setName('auth.login');
        $router->post('/login', [SessionController::class, 'store']);
        $router->delete('/logout', [SessionController::class, 'destroy'])->setName('auth.logout');

        $router->get('/password/reset', [PasswordController::class, 'formReset'])->setName('auth.password_reset');
        $router->post('/password/reset', [PasswordController::class, 'reset']);
        $router->get('/password/recover/{id}/{token}', [PasswordController::class, 'recover'])
            ->setName('auth.password_recover');
        $router->post('/password/recover/{id}/{token}', [PasswordController::class, 'recover'])
            ->setName('auth.password_recover');
    }
}
