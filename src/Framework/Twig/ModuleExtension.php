<?php

namespace Framework\Twig;

/**
 * Permet de rajouter la fonction module_enabled() à Twig.
 */
class ModuleExtension extends \Twig_Extension
{
    /**
     * @var string[]
     */
    private $modules;

    public function __construct(array $modules)
    {
        $this->modules = $modules;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('has_module', [$this, 'hasModule'])
        ];
    }

    public function hasModule(string $moduleName): bool
    {
        $moduleName = ucfirst($moduleName) . 'Module';
        foreach ($this->modules as $module) {
            if (mb_strpos($module, $moduleName) !== false) {
                return true;
            }
        }

        return false;
    }
}
