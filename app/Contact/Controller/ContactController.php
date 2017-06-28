<?php

namespace App\Contact\Controller;

use Core\Controller;
use Core\Validator;
use Psr\Http\Message\ServerRequestInterface;

class ContactController extends Controller
{
    public function contact(ServerRequestInterface $request, \Swift_Mailer $mailer)
    {
        if ($request->getMethod() === 'POST') {
            $message = $request->getParsedBody();
            $errors = $this->validates($message);
            if (empty($errors)) {
                $mail = new \Swift_Message('Formulaire de contact');
                $mail->setBody($this->render('@contact/mail', $message));
                $mail->addPart($this->render('@contact/mail.html', $message), 'text/html');
                $mail->setTo($this->container->get('mailer.webmaster'));
                $mail->setFrom($this->container->get('mailer.webmaster'));
                $mailer->send($mail);
                $this->flash('success', 'Merci pour votre message');

                return $this->redirect('contact');
            }
        }

        return $this->lol;

        return $this->render('@contact/contact', compact('message', 'errors'));
    }

    public function validates(array $params): array
    {
        return (new Validator($params))
            ->required('name', 'email', 'message')
            ->minLength('message', 25)
            ->minLength('name', 5)
            ->email('email')
            ->getErrors();
    }
}
