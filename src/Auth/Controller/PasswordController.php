<?php

namespace App\Auth\Controller;

use App\Auth\Table\UserTable;
use Framework\Controller;
use Framework\Mail;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class PasswordController extends Controller
{
    /**
     * Affiche le formulaire de demande de mot de passe.
     *
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public function formReset()
    {
        return $this->render('@auth/password/reset');
    }

    /**
     * Envoie l'email de rappel de mot de passe.
     *
     * @param ServerRequestInterface $request
     * @param UserTable              $userTable
     * @param Mail                   $mail
     *
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public function reset(ServerRequestInterface $request, UserTable $userTable, Mail $mail)
    {
        $params = $request->getParsedBody();
        $errors = (new Validator($params))
            ->required('email')
            ->email('email')
            ->getErrors();
        if (empty($errors)) {
            $user = $userTable->findByEmail($params['email']);
            if ($user) {
                $token = bin2hex(random_bytes(20));
                $userTable->update($user->id, [
                    'password_reset_token' => $token,
                    'password_reset_at'    => date('Y-m-d H:i:s')
                ]);
                $mail->to($params['email'])
                    ->text('@auth/email/reset', compact('user', 'token'))
                    ->send();
            }
            $this->flash('success', 'La procédure de réinitialisation de mot de passe a été envoyée');

            return $this->redirect('auth.password_reset');
        }

        return $this->render('@auth/password/reset', compact('errors', 'params'));
    }

    /**
     * Permet de réinitialiser son mot de passe.
     *
     * @param int                    $id
     * @param string                 $token
     * @param ServerRequestInterface $request
     * @param UserTable              $userTable
     *
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public function recover(int $id, string $token, ServerRequestInterface $request, UserTable $userTable)
    {
        /* @var $user \App\Auth\Entity\User */
        $user = $userTable->find($id);
        if ($user && $user->isTokenValid($token)) {
            if ($request->getMethod() === 'POST') {
                $params = $request->getParsedBody();
                $errors = (new Validator($params))
                    ->required('password')
                    ->confirm('password')
                    ->getErrors();
                if (empty($errors)) {
                    $userTable->update($id, [
                        'password_reset_token' => null,
                        'password_reset_at'    => null,
                        'password'             => password_hash($params['password'], PASSWORD_DEFAULT)
                    ]);
                    $this->flash('success', 'Votre mot de passe a bien été réinitialisé');

                    return $this->redirect('auth.login');
                }
            }

            return $this->render('@auth/password/recover', compact('errors'));
        }
        $this->flash('error', 'Ce token ne semble pas valide');

        return $this->redirect('auth.password_reset');
    }
}
