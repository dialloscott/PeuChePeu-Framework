<?php

namespace Core\Database;

use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

/**
 * Permet de construire une requête paginée.
 */
class PaginatedQuery implements AdapterInterface
{
    /**
     * @var string
     */
    private $query;

    /**
     * @var int
     */
    private $count;

    /**
     * @var Database
     */
    private $database;

    /**
     * @var null|string
     */
    private $entity;

    /**
     * @var array
     */
    private $params;

    /**
     * @param Database    $database
     * @param string      $query
     * @param array       $params
     * @param int         $count
     * @param null|string $entity
     */
    public function __construct(Database $database, string $query, array $params, int $count, ?string $entity = null)
    {
        $this->query = $query;
        $this->count = $count;
        $this->database = $database;
        $this->entity = $entity;
        $this->params = $params;
    }

    /**
     * Returns the number of results.
     *
     * @return int the number of results
     */
    public function getNbResults()
    {
        return $this->count;
    }

    /**
     * Récupère une partie des résultat depuis la base de données.
     *
     * @param int $offset
     * @param int $length
     *
     * @return array
     */
    public function getSlice($offset, $length): array
    {
        $offset = (int) $offset;
        $length = (int) $length;

        return $this->database->fetchAll("{$this->query} LIMIT $offset, $length", $this->params, $this->entity);
    }

    /**
     * Renvoie les résultats sous forme d'instance de PagerFanta.
     *
     * @return Pagerfanta
     */
    public function getPaginator(): Pagerfanta
    {
        return new Pagerfanta($this);
    }
}
