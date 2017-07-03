<?php

namespace App\Registration\Controller;

use App\Auth\Table\UserTable;
use Framework\Controller;
use Psr\Http\Message\ServerRequestInterface;

class AccountController extends Controller
{
    /**
     * Permet de consulter son compte.
     *
     * @param ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public function account(ServerRequestInterface $request)
    {
        return $this->render('@registration/account', [
            'user' => $request->getAttribute('user')
        ]);
    }

    /**
     * Permet de supprimer son compte.
     *
     * @param ServerRequestInterface $request
     * @param UserTable              $userTable
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete(ServerRequestInterface $request, UserTable $userTable)
    {
        $user = $request->getAttribute('user');
        $userTable->delete($user->id);
        $this->flash('success', 'Votre compte a bien été supprimé');

        return $this->redirect('auth.login');
    }
}
