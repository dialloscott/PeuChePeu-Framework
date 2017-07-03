<?php

namespace App\Auth\Middleware;

use App\Auth\AuthService;
use App\Auth\Exception\ForbiddenException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoggedinMiddleware
{
    /**
     * @var AuthService
     */
    private $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        $user = $this->auth->user();
        if ($user) {
            return $response = $next($request->withAttribute('user', $user), $response);
        }
        $forbiddenException = new ForbiddenException($request);
        $forbiddenException->request = $request;
        throw $forbiddenException;
    }
}
