<?php

namespace Core\View;

/**
 * Class View
 * Permet d'intéragir avec la gestion de template (ici Twig).
 */
class TwigView implements ViewInterface
{
    /**
     * @var \Twig_Loader_Filesystem
     */
    private $loader;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(array $extensions, $cachePath)
    {
        $this->loader = new \Twig_Loader_Filesystem();
        $this->twig = new \Twig_Environment($this->loader, [
            'cache' => $cachePath
        ]);
        // Ajout des extensions
        foreach ($extensions as $extension) {
            $this->twig->addExtension($extension);
        }
    }

    /**
     * Permet d'enregistrer un namespace pour les vues.
     *
     * @param string $namespace
     * @param string $path
     */
    public function addPath(string $path, string $namespace = \Twig_Loader_Filesystem::MAIN_NAMESPACE)
    {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Rend une vue.
     *
     * @param string $viewName
     * @param array  $data
     *
     * @return string
     */
    public function render(string $viewName, array $data = []): string
    {
        return $this->twig->render($viewName . '.twig', $data);
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig(): \Twig_Environment
    {
        return $this->twig;
    }
}
