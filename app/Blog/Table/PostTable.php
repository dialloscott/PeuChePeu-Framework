<?php

namespace App\Blog\Table;

use App\Blog\PostEntity;
use Core\Database\NoRecordException;
use Core\Database\PaginatedQuery;
use Core\Database\Table;

/**
 * Permet de récupérer les articles depuis la base de données.
 */
class PostTable extends Table
{
    public const TABLE = 'posts';
    public const ENTITY = PostEntity::class;

    /**
     * Récupère les données paginées.
     *
     * @param int $perPage
     * @param int $currentPage
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function findPaginated($perPage = 10, $currentPage = 1)
    {
        $count = $this->database->fetchColumn('SELECT COUNT(id) FROM posts');

        return (new PaginatedQuery(
            $this->database,
            'SELECT * FROM posts ORDER BY created_at DESC',
            $count,
            PostEntity::class
        ))
            ->getPaginator()
            ->setCurrentPage($currentPage)
            ->setMaxPerPage($perPage);
    }

    /**
     * Récupère un enregistrement à partir de son slug.
     *
     * @param string $slug
     *
     * @throws NoRecordException
     *
     * @return mixed
     */
    public function findBySlug(string $slug)
    {
        $result = $this->database->fetch('SELECT * FROM posts WHERE slug = ?', [$slug]);
        if ($result === false) {
            throw new NoRecordException();
        }

        return $result;
    }
}
