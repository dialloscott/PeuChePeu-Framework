<?php

namespace App\Blog\Table;

use App\Blog\PostEntity;
use Framework\Database\NoRecordException;
use Framework\Database\PaginatedQuery;
use Framework\Database\Table;

/**
 * Permet de récupérer les articles depuis la base de données.
 */
class PostTable extends Table
{
    public const TABLE = 'posts';
    public const ENTITY = PostEntity::class;

    public function findLatest()
    {
        return $this->database->fetchAll('SELECT 
              posts.*,
              categories.name as category_name, categories.slug as category_slug
            FROM posts 
            LEFT JOIN categories ON categories.id = posts.category_id
            ORDER BY created_at DESC
            LIMIT 4', [], PostEntity::class);
    }

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
            'SELECT 
              posts.*,
              categories.name as category_name, categories.slug as category_slug
            FROM posts 
            LEFT JOIN categories ON categories.id = posts.category_id
            ORDER BY created_at DESC',
            [],
            $count,
            PostEntity::class
        ))
            ->getPaginator()
            ->setCurrentPage($currentPage)
            ->setMaxPerPage($perPage);
    }

    /**
     * Récupère les données paginées.
     *
     * @param int $perPage
     * @param int $currentPage
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function findPaginatedByCategory($perPage, $currentPage, string $categorySlug)
    {
        $count = $this->database->fetchColumn('
          SELECT COUNT(posts.id) 
          FROM posts INNER JOIN categories ON categories.id = posts.category_id
          WHERE categories.slug = ?', [$categorySlug]);

        return (new PaginatedQuery(
            $this->database,
            'SELECT 
              posts.*,
              categories.name as category_name, categories.slug as category_slug
            FROM posts 
            LEFT JOIN categories ON categories.id = posts.category_id
            WHERE categories.slug = ?
            ORDER BY created_at DESC',
            [$categorySlug],
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
        $result = $this->database->fetch('
          SELECT 
            posts.*, 
            categories.name as category_name, categories.slug as category_slug 
          FROM posts 
          LEFT JOIN categories ON categories.id = posts.category_id
          WHERE posts.slug = ?
        ', [$slug]);
        if ($result === false) {
            throw new NoRecordException();
        }

        return $result;
    }
}
