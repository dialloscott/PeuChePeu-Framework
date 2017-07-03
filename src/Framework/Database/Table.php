<?php

namespace Framework\Database;

/**
 * Représente une table en base de données.
 */
class Table
{
    /**
     * @var Database
     */
    protected $database;

    /**
     * Nom de la table en abse de données.
     */
    public const TABLE = null;

    /**
     * Permet de définir dans quel entité sauvegarder les résultats.
     */
    protected const ENTITY = null;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Récupère un enregistrement en se basant sur l'ID.
     *
     * @param int $id
     *
     * @return \stdClass
     */
    public function find(int $id)
    {
        return $this->database->fetch('SELECT * FROM ' . static::TABLE . ' WHERE id = ?', [$id], static::ENTITY);
    }

    /**
     * écupère un enregistrement en se basant sur l'ID et renvoie une exception si l'entité n'existe pas.
     *
     * @param $id
     *
     * @throws NoRecordException
     *
     * @return \stdClass
     */
    public function findOrFail(int $id)
    {
        $record = $this->find($id);
        if (!$record) {
            throw new NoRecordException('Aucun enregistrement ' . static::TABLE . '::' . $id);
        }

        return $record;
    }

    public function findList(string $field)
    {
        $records = $this->database->fetchAll('SELECT id, ' . $field . ' FROM ' . static::TABLE, [], static::ENTITY);
        $results = [];
        foreach ($records as $record) {
            $results[$record->id] = $record->$field;
        }

        return $results;
    }

    public function findAll()
    {
        return $this->database->fetchAll('SELECT * FROM ' . static::TABLE, [], static::ENTITY);
    }

    /**
     * Supprime un enregistrement.
     *
     * @param int $id
     *
     * @return \PDOStatement
     */
    public function delete(int $id): \PDOStatement
    {
        return $this->database->query('DELETE FROM ' . static::TABLE . ' WHERE id = ?', [$id]);
    }

    /**
     * Met à jour un enregistrement
     * Attention, les clefs ne sont pas échapées !
     *
     * @param int   $id
     * @param array $params
     *
     * @return \PDOStatement
     */
    public function update(int $id, array $params): \PDOStatement
    {
        $query = implode(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
        $params['id'] = $id;

        return $this->database->query('UPDATE ' . static::TABLE . ' SET ' . $query . ' WHERE id = :id', $params);
    }

    /**
     * Crée un nouvel enregistrement.
     *
     * @param array $params
     *
     * @return int|null
     */
    public function create(array $params): ?int
    {
        $query = implode(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
        $this->database->query('INSERT INTO ' . static::TABLE . ' SET ' . $query, $params);

        return $this->database->lastInsertId();
    }

    /**
     * Compte le nombre d'enregistrement.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->database->fetchColumn('SELECT COUNT(id) FROM ' . static::TABLE);
    }

    /**
     * Récupère l'instance de la base de données.
     *
     * @return Database
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }

    /**
     * Renvoie la table utilisée.
     *
     * @return null|string
     */
    public function getTable(): ?string
    {
        return static::TABLE;
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
        $table = static::TABLE;
        $count = $this->database->fetchColumn('SELECT COUNT(id) FROM ' . $table);

        return (new PaginatedQuery(
            $this->database,
            'SELECT * FROM ' . $table . ' ORDER BY id DESC',
            [],
            $count,
            static::ENTITY
        ))
            ->getPaginator()
            ->setCurrentPage($currentPage)
            ->setMaxPerPage($perPage);
    }
}
