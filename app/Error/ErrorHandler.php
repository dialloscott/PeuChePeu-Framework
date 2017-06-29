<?php

namespace App\Error;

use Core\View\ViewInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Body;
use UnexpectedValueException;

/**
 * Default Slim application error handler.
 *
 * It outputs the error message and diagnostic information in either JSON, XML,
 * or HTML based on the Accept header.
 */
class ErrorHandler
{
    /**
     * @var ViewInterface
     */
    private $view;

    public function __construct(ViewInterface $view)
    {
        $this->view = $view;
    }

    /**
     * Invoke error handler.
     *
     * @param ServerRequestInterface $request   The most recent Request object
     * @param ResponseInterface      $response  The most recent Response object
     * @param \Exception             $exception The caught Exception object
     *
     * @throws UnexpectedValueException
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \Exception $exception)
    {
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($this->view->render('@error/400'));

        return $response
            ->withStatus(404)
            ->withHeader('Content-type', 'text/html')
            ->withBody($body);
    }
}
