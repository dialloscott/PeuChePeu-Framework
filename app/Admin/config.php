<?php

return [
    'admin.prefix'                 => '/admin',
    'admin.role'                   => 'admin',
    'admin.widgets'                => \DI\get(\App\Admin\AdminWidgets::class),
    'admin.middleware'             => \DI\object(\App\Auth\Middleware\RoleMiddleware::class)
                                        ->constructor(
                                            \DI\get('auth.service'),
                                            \DI\get('admin.role')
                                        ),
];
