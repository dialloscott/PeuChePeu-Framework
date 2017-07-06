<?php

use Schnittstabil\Psr7\Csrf\MiddlewareBuilder as CsrfMiddlewareBuilder;

return [
    // Env
    'dev'                                        => true,

    // Chemins
    'basepath'                                   => dirname(dirname(__DIR__)),
    'settings.addContentLengthHeader'            => false,
    'settings.displayErrorDetails'               => true,
    'settings.routerCacheFile'                   => function (\Psr\Container\ContainerInterface $c) {
        if ($c->get('dev')) {
            return false;
        }

        return $c->get('basepath') . '/tmp/routes';
    },
    'settings.determineRouteBeforeAppMiddleware' => true,
    'settings'                                   => \DI\add(['debug' => \DI\get('dev')]),
    'errorHandler'                               => \DI\object(\Framework\Handler::class),
    'upload_path'                                => \DI\string('{basepath}/public/uploads'),

    // Misc
    \Slim\Interfaces\RouterInterface::class      => \DI\object(\Slim\Router::class),

    // Vue
    'view.cache'                                 => \DI\string('{basepath}/tmp/views'),
    'twig.extensions'                            => [
        \DI\object(\Framework\Twig\ModuleExtension::class)->constructor(\DI\factory(['app', 'getModules'])),
        \DI\object(\Framework\Twig\RouterExtension::class)->constructor(
            \DI\get('router'),
            \DI\factory(['request', 'getUri'])
        ),
        \DI\object(\Knlv\Slim\Views\TwigMessages::class)->constructor(\DI\get(\Slim\Flash\Messages::class)),
        \DI\object(\Framework\Twig\PagerfantaExtension::class)->constructor(\DI\get('router')),
        \DI\object(\Framework\Twig\TimeExtension::class),
        \DI\object(\Framework\Twig\CsrfExtension::class)->constructor(\DI\get('csrf.name'), \DI\get('csrf')),
        \DI\object(\Framework\Twig\TextExtension::class),
    ],
    'view'                                       => function (\Psr\Container\ContainerInterface $c) {
        return new \Framework\View\TwigView($c->get('twig.extensions'), $c->get('dev') ? false : $c->get('view.cache'));
    },
    \Framework\View\ViewInterface::class         => \DI\get('view'),

    // Session
    'session'                                    => \DI\object(\Framework\Session\Session::class),
    'session.flash'                              => \DI\object(\Slim\Flash\Messages::class)
        ->constructor(\DI\get('session')),
    \Framework\Session\SessionInterface::class   => \DI\get('session'),
    \Slim\Flash\Messages::class                  => \DI\get('session.flash'),

    // CSRF
    'csrf.name'                                  => 'X-XSRF-TOKEN',
    'csrf.key'                                   => \DI\env('csrf_key', 'fake key'),
    'csrf'                                       => function (\Psr\Container\ContainerInterface $c) {
        return CsrfMiddlewareBuilder::create($c->get('csrf.key'))
            ->buildSynchronizerTokenPatternMiddleware($c->get('csrf.name'));
    },
    \Framework\Twig\CsrfExtension::class         => \DI\object()
        ->constructor(
            \DI\get('csrf.name'),
            \DI\get('csrf')
        ),

    // Database
    'db_name'                                    => \DI\env('db_name'),
    'db_username'                                => \DI\env('db_username', 'root'),
    'db_password'                                => \DI\env('db_password', 'root'),
    'db_host'                                    => \DI\env('db_host', '127.0.0.1'),
    \Framework\Database\Database::class          => \DI\object()->constructor(
        \DI\get('db_name'),
        \DI\get('db_username'),
        \DI\get('db_password'),
        \DI\get('db_host')
    ),
    'db'                                         => \DI\get(\Framework\Database\Database::class),

    // Mailer
    'mailer.webmaster'                           => 'demo@local.dev',
    'mailer.transport'                           => \DI\object(Swift_SmtpTransport::class)
        ->constructor('localhost', 1025),
    'mailer'                                     => \DI\object(Swift_Mailer::class)
        ->constructor(\DI\get('mailer.transport')),
    Swift_Mailer::class                          => \DI\get('mailer'),
    \Framework\Mail::class                       => \DI\factory(function (\Psr\Container\ContainerInterface $c) {
        return new \Framework\Mail(
            $c->get('mailer.webmaster'),
            $c->get('mailer'),
            $c->get('view')
        );
    })
];
