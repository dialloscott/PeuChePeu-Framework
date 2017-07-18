<?php

return [
    'twig.extensions'     => \DI\add([
       \DI\object(\App\Auth\Twig\AuthTwigExtension::class)->constructor(\DI\get('auth.service'))
    ]),
    'auth.service'            => \DI\object(\App\Auth\AuthService::class),
    'auth.loggedInMiddleware' => \DI\object(\App\Auth\Middleware\LoggedinMiddleware::class)
        ->constructor(\DI\get('auth.service')),
    'auth.roleMiddleware' => \DI\factory(\App\Auth\Middleware\RoleMiddleware::class)
        ->parameter('auth', \DI\get('auth.service')),
    'auth.redirectMiddleware' => \DI\object(\App\Auth\Middleware\RedirectLoginMiddleware::class)
        ->constructor(
            \DI\get(\Framework\Session\SessionInterface::class),
            \DI\get('router'),
            \DI\get('session.flash')
        ),
    'auth.user' => \DI\factory(function ($auth) {
        return $auth->user();
    })
        ->parameter('auth', \DI\get('auth.service')),
    \App\Auth\Entity\User::class => \DI\get('auth.user')
];
