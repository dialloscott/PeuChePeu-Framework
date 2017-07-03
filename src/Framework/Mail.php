<?php

namespace Framework;

use Framework\View\ViewInterface;

class Mail
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var ViewInterface
     */
    private $view;

    public function __construct(string $from, \Swift_Mailer $mailer, ViewInterface $view)
    {
        $this->message = new \Swift_Message();
        $this->message->setFrom($from);
        $this->from = $from;
        $this->mailer = $mailer;
        $this->view = $view;
    }

    public function to(string $email): self
    {
        $this->message->setTo($email);

        return $this;
    }

    public function text(string $viewPath, array $params = []): self
    {
        $this->message->addPart($this->view->render($viewPath, $params));

        return $this;
    }

    public function html(string $viewPath, array $params = []): self
    {
        $this->message->addPart($this->view->render($viewPath, $params), 'text/html');

        return $this;
    }

    public function send(): self
    {
        $this->mailer->send($this->message);

        return $this;
    }
}
