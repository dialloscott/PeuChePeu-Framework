<?php

namespace Framework;

use DI\ContainerBuilder;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

class App extends \DI\Bridge\Slim\App
{
    /**
     * Ajoute une définition au chargement.
     *
     * @var string|array
     */
    private $definitions;

    /**
     * Liste tous les modules disponibles dans l'application.
     *
     * @var array
     */
    private $modules;

    public function __construct($definitions = [], array $modules = [])
    {
        $this->definitions = $definitions;
        $this->modules = $modules;

        // On construit le conteneur
        parent::__construct();

        // Middlewares
        if ($this->getContainer()->get('dev')) {
            $this->add(new WhoopsMiddleware());
        }
        $this->add($this->getContainer()->get('csrf'));

        // On charge les modules
        foreach ($modules as $module) {
            $this->getContainer()->get($module);
        }
    }

    /**
     * Permet de configurer le conteneur d'injection de dépendances.
     *
     * @param ContainerBuilder $builder
     */
    protected function configureContainer(ContainerBuilder $builder): void
    {
        // PHP-DI
        $builder->addDefinitions(__DIR__ . '/config.php');
        $builder->addDefinitions($this->definitions);
        $builder->addDefinitions([
            'app'            => $this,
            get_class($this) => $this
        ]);
        foreach ($this->modules as $module) {
            if ($module::DEFINITIONS) {
                $builder->addDefinitions($module::DEFINITIONS);
            }
        }
    }

    /**
     * Récupère la liste des modules disponibles.
     *
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }
}
