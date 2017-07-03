<?php

namespace App\Registration\Twig;

use Framework\View\ViewInterface;

class RegistrationExtension extends \Twig_Extension
{
    /**
     * @var ViewInterface
     */
    private $view;

    public function __construct(ViewInterface $view)
    {
        $this->view = $view;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('registration_form', [$this, 'renderForm'], ['is_safe' => ['html']])
        ];
    }

    public function renderForm()
    {
        return $this->view->render('@registration/form');
    }
}
