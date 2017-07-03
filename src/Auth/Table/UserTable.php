<?php

namespace App\Auth\Table;

use App\Auth\Entity\User;
use Framework\Database\Table;

class UserTable extends Table
{
    public const TABLE = 'users';
    public const ENTITY = User::class;

    /**
     * Récupère un utilisateur depuis son nom d'utilisateur.
     *
     * @param string $username
     *
     * @return User|null
     */
    public function findByUsername(string $username): ?User
    {
        return $this->database->fetch(
            'SELECT * FROM ' . $this->getTable() . ' WHERE username = ?',
            [$username],
            User::class
        ) ?: null;
    }

    public function findByEmail($email): ?User
    {
        return $this->database->fetch(
            'SELECT * FROM ' . $this->getTable() . ' WHERE email = ?',
            [$email],
            User::class
        ) ?: null;
    }
}
