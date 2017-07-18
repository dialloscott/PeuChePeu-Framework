<?php

namespace Framework;

use DI\Container;
use Framework\View\ViewInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class Controller.
 */
class Controller
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Controller constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Permet de rendre une vue.
     *
     * @param string $filename Nom de la vue à rendre
     * @param array  $data     Données à envoyer à la vue
     *
     * @return ResponseInterface|string
     */
    protected function render(string $filename, array $data = []): string
    {
        return $this->container->get(ViewInterface::class)->render($filename, $data);
    }

    /**
     * Renvoie une réponse de redirection.
     *
     * @param string $path
     * @param array  $params
     *
     * @return ResponseInterface
     */
    protected function redirect(string $path, array $params = []): ResponseInterface
    {
        $response = new Response();
        $router = $this->container->get(Router::class);

        return $response->withHeader('Location', $router->pathFor($path, $params));
    }

    /**
     * Envoie un message flash.
     *
     * @param string $type
     * @param string $message
     */
    protected function flash(string $type, string $message): void
    {
        $this->getFlash()->addMessage($type, $message);
    }

    /**
     * Récupère le système de message flash.
     *
     * @return Messages
     */
    protected function getFlash(): Messages
    {
        return $this->container->get(Messages::class);
    }
}
