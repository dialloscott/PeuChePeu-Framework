<?php

namespace App\Auth\Entity;

class User
{
    public $password;
    public $username;
    public $id;
    public $password_reset_token;
    public $password_reset_at;

    /**
     * @var array Liste les rôles de l'utilisateur
     */
    public $roles = [];

    public function __construct()
    {
        $this->roles = ['admin'];
    }

    public function checkPassword(string $password)
    {
        return password_verify($password, $this->password);
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    public function isTokenValid(string $token): bool
    {
        if ($this->password_reset_token !== $token) {
            return false;
        }
        $resetAt = new \DateTime($this->password_reset_at);
        $diff = (time() - $resetAt->getTimestamp()) / 3600;
        if ($diff > 1) {
            return false;
        }

        return true;
    }
}
