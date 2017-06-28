<?php

return [
    'auth.service'        => \DI\object(\App\Auth\AuthService::class),
    'auth.roleMiddleware' => \DI\factory(\App\Auth\Middleware\RoleMiddleware::class)
        ->parameter('auth', \DI\get('auth.service'))
];
