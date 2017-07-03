<?php

namespace Framework\Twig;

use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouterInterface;

class RouterExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var UriInterface
     */
    private $uri;

    public function __construct(RouterInterface $router, UriInterface $uri)
    {
        $this->router = $router;
        $this->uri = $uri;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('path_for', [$this, 'pathFor']),
            new \Twig_SimpleFunction('path_child_of', [$this, 'isChild'])
        ];
    }

    public function pathFor(string $path, array $data = [], array $queryParams = []): string
    {
        $prefix = '';
        if (isset($data['full'])) {
            $prefix = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]";
        }

        return $prefix . $this->router->pathFor($path, $data, $queryParams);
    }

    public function isChild(string $path)
    {
        return mb_strpos($this->uri->getPath(), $this->router->pathFor($path)) !== false;
    }
}
