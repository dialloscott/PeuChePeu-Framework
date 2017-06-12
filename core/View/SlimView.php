<?php

namespace Core\View;

use Core\Twig\Extensions\ModuleExtension;
use Core\Twig\Extensions\PagerfantaExtension;
use Core\Twig\Extensions\SlimExtension;
use Core\Twig\Extensions\TextExtension;
use Slim\Views\TwigExtension;

/**
 * Class View
 * Permet d'intéragir avec la gestion de template (ici Twig)
 *
 * @package Core
 */
class SlimView implements ViewInterface
{

    /**
     * @var \Twig_Loader_Filesystem
     */
    private $loader;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(TwigExtension $slimExtension, ModuleExtension $moduleExtension, PagerfantaExtension
    $pagerfantaExtension)
    {
        $this->loader = new \Twig_Loader_Filesystem();
        $this->twig = new \Twig_Environment($this->loader, [
            'cache' => false, // $basepath . '/tmp/cache'
        ]);
        // Ajout des extensions
        $this->twig->addExtension($moduleExtension);
        $this->twig->addExtension($slimExtension);
        $this->twig->addExtension($pagerfantaExtension);
        $this->twig->addExtension(new TextExtension());
    }

    /**
     * Permet d'enregistrer un namespace pour les vues
     *
     * @param string $namespace
     * @param string $path
     */
    public function addPath(string $path, $namespace = \Twig_Loader_Filesystem::MAIN_NAMESPACE)
    {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Rend une vue
     *
     * @param string $viewName
     * @param array $data
     * @return string
     */
    public function render (string $viewName, array $data = []): string {
        return $this->twig->render($viewName . '.twig', $data);
    }

}