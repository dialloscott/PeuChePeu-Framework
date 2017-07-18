<?php

namespace App\Auth\Middleware;

use App\Auth\Exception\ForbiddenException;
use Framework\Session\SessionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages;
use Slim\Http\Response;
use Slim\Interfaces\RouterInterface;

class RedirectLoginMiddleware
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Messages
     */
    private $flash;

    public function __construct(SessionInterface $session, RouterInterface $router, Messages $flash)
    {
        $this->session = $session;
        $this->router = $router;
        $this->flash = $flash;
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        try {
            $response = $next($request, $response);
        } catch (ForbiddenException $e) {
            $this->session->set('auth.redirect', (string) $request->getUri());
            $this->flash->addMessage('error', 'Vous devez être connecté pour voir la page suivante');

            return (new Response())
                ->withStatus(500)
                ->withHeader('Location', $this->router->pathFor('auth.login'));
        }

        return $response;
    }
}
