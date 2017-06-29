<?php

namespace App\Registration\Controller;

use App\Auth\Table\UserTable;
use Core\Controller;
use Core\Validator;
use Psr\Http\Message\ServerRequestInterface;

class RegistrationController extends Controller
{
    public function register(ServerRequestInterface $request, UserTable $userTable)
    {
        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();
            $errors = $this->validates($params, $userTable);
            if (empty($errors)) {
                $userTable->create([
                    'username' => $params['username'],
                    'password' => password_hash($params['password'], PASSWORD_DEFAULT),
                    'email'    => $params['email']
                ]);
                $this->flash('success', 'Votre compte a bien été créé');

                return $this->redirect('auth.login');
            }
            $user = $params;
        }

        return $this->render('@registration/register', compact('errors', 'user'));
    }

    private function validates(array $params, UserTable $userTable)
    {
        return (new Validator($params))
            ->setDatabase($userTable->getDatabase())
            ->required('email', 'username', 'password', 'password_confirm')
            ->email('email')
            ->unique('email', $userTable->getTable())
            ->unique('username', $userTable->getTable())
            ->minLength('username', 4)
            ->maxLength('username', 20)
            ->confirm('password')
            ->minLength('password', 4)
            ->getErrors();
    }
}
