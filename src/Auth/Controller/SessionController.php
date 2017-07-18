<?php

namespace App\Auth\Controller;

use App\Auth\AuthService;
use Framework\Controller;
use Framework\Session\SessionInterface;
use Framework\View\ViewInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class SessionController extends Controller
{
    public function create(ViewInterface $view)
    {
        $redirectMessages = $this->getFlash()->getMessage('redirect');
        $redirect = count($redirectMessages) > 0 ? $redirectMessages[0] : null;

        return $view->render('@auth/login', compact('redirect'));
    }

    public function store(Request $request, AuthService $auth, SessionInterface $session)
    {
        $username = $request->getParam('username');
        $password = $request->getParam('password');
        $redirect = $session->get('auth.redirect') ?: '/';
        $user = $auth->login($username, $password);
        if ($user) {
            $this->flash('success', 'Vous êtes maintenant connecté');

            return (new Response())->withHeader('Location', $redirect);
        }
        $this->flash('error', 'Mot de passe ou identifiant incorrect');

        return $this->redirect('auth.login');
    }

    public function destroy(Response $response, AuthService $auth)
    {
        $auth->logout();
        $this->flash('success', 'Vous êtes maintenant déconnecté');

        return $response->withAddedHeader('location', '/');
    }
}
