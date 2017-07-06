<?php

return [
    'twig.extensions'     => \DI\add([
       \DI\object(\App\Auth\Twig\AuthTwigExtension::class)->constructor(\DI\get('auth.service'))
    ]),
    'auth.service'        => \DI\object(\App\Auth\AuthService::class),
    'auth.roleMiddleware' => \DI\factory(\App\Auth\Middleware\RoleMiddleware::class)
        ->parameter('auth', \DI\get('auth.service'))
];
