<?php

return [
    \App\Admin\AdminModule::class  => \DI\object()
        ->constructorParameter('roleMiddleware', \DI\get('admin.middleware'))
        ->constructorParameter('prefix', \DI\get('admin.prefix')),
    'admin.prefix'                 => '/admin',
    'admin.role'                   => 'admin',
    'admin.widgets'                => \DI\get(\App\Admin\AdminWidgets::class),
    'admin.middleware'             => \DI\object(\App\Auth\Middleware\RoleMiddleware::class)
                                        ->constructor(
                                            \DI\get('auth.service'),
                                            \DI\get('admin.role')
                                        ),
];
