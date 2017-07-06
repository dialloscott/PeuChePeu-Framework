<?php

namespace Framework;

use DateTime;
use Framework\Database\Database;
use Framework\Database\Table;
use Psr\Http\Message\UploadedFileInterface;

class Validator
{
    private const MIME_TYPES = [
        'jpe'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'pdf'  => 'application/pdf'
    ];

    /**
     * @var array Stocke les erreurs de validation
     */
    private $errors = [];

    /**
     * @var Database
     */
    private $database;

    /**
     * @var array
     */
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Vérifie si une clef existe dans le tableau.
     *
     * @param string[] ...$keys
     *
     * @return self
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            if (!isset($this->params[$key]) || empty($this->params[$key])) {
                $this->errors[$key] = 'Le champs est vide';
            }
        }

        return $this;
    }

    /**
     * Vérifie le formatage d'une date.
     *
     * @param $key
     *
     * @return self
     */
    public function dateTime($key): self
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $this->getValue($key));
        $errors = DateTime::getLastErrors();
        if (!empty($errors['warning_count']) || $dateTime === false) {
            $this->errors[$key] = 'La date ne semble pas valide';
        }

        return $this;
    }

    /**
     * Limite la taille minimale de la chaine.
     *
     * @param $key
     * @param int $length
     *
     * @return self
     */
    public function minLength($key, int $length): self
    {
        if (mb_strlen($this->getValue($key)) < $length) {
            $this->errors[$key] = "Vous ne pouvez pas écrire moins de $length caractères";
        }

        return $this;
    }

    /**
     * Limite la taille maximale de la chaine.
     *
     * @param $key
     * @param int $length
     *
     * @return self
     */
    public function maxLength($key, int $length): self
    {
        if (mb_strlen($this->getValue($key)) > $length) {
            $this->errors[$key] = "Vous ne pouvez pas écrire plus de $length caractères";
        }

        return $this;
    }

    /**
     * Limite la chaine pour un slug.
     *
     * @param $key
     *
     * @return self
     */
    public function slug($key): self
    {
        $pattern = '/^([a-z0-9]+-?)+$/';
        if (!preg_match($pattern, $this->getValue($key))) {
            $this->errors[$key] = 'Le slug ne semble pas valide';
        }

        return $this;
    }

    /**
     * Vérifie si le fichier a bien été uploadé.
     *
     * @param $key
     *
     * @return self
     */
    public function uploaded($key): self
    {
        /** @var UploadedFileInterface $file */
        $file = $this->getValue($key);
        if (null === $file || $file->getError() !== UPLOAD_ERR_OK) {
            $this->errors[$key] = 'Vous devez uploader un fichier';
        }

        return $this;
    }

    /**
     * Vérifie si l'extension du fichier est correcte.
     *
     * @param $key
     * @param array $extensions
     *
     * @return self
     */
    public function extension($key, array $extensions): self
    {
        /** @var UploadedFileInterface $file */
        $file = $this->getValue($key);
        if (null !== $file && $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $expectedType = array_key_exists($extension, static::MIME_TYPES) ? static::MIME_TYPES[$extension] : null;
            if (!in_array($extension, $extensions, true) ||
                $type !== $expectedType
            ) {
                $this->errors[$key] = 'Le fichier ne semble pas valide';
            }
        }

        return $this;
    }

    /**
     * Vérifie que le champs est unique dans la table.
     *
     * @param $key
     * @param string   $table
     * @param int|null $id    Id de l'élément à ne pas prendre en compte (lui-même)
     *
     * @return self
     */
    public function unique($key, string $table, ?int $id = null): self
    {
        $value = $this->getValue($key);
        $query = 'SELECT id FROM ' . $table . " WHERE $key = ?";
        $params = [$value];
        if ($id) {
            $query .= ' AND id != ?';
            $params[] = $id;
        }
        if (!empty($this->database->fetchColumn($query, $params))) {
            $this->errors[$key] = 'Cette valeur est déjà utilisée';
        }

        return $this;
    }

    /**
     * Vérifie si un enregistrement existe dans la table indiquée.
     *
     * @param $key
     * @param string $table
     *
     * @return self
     */
    public function exists($key, string $table): self
    {
        $value = $this->getValue($key);
        if (!$value) {
            $this->errors[$key] = 'Aucun enregistrement ne correspond à cet ID';

            return $this;
        }
        $query = 'SELECT id FROM ' . $table . ' WHERE id = ?';
        if (empty($this->database->fetchColumn($query, [$value]))) {
            $this->errors[$key] = 'Aucun enregistrement ne correspond à cet ID';
        }

        return $this;
    }

    /**
     * Vérifie qu'un email est valide.
     *
     * @param string $key
     *
     * @return self
     */
    public function email(string $key): self
    {
        $value = $this->getValue($key);
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[$key] = 'Cet email ne semble pas valide';
        }

        return $this;
    }

    /**
     * Un champs key_confirm doit être présent avec la même valeur que le champs initial.
     *
     * @param string $key
     *
     * @return Validator
     */
    public function confirm(string $key): self
    {
        $value = $this->getValue($key);
        $valueConfirmed = $this->getValue($key . '_confirm');
        if ($value !== $valueConfirmed) {
            $this->errors[$key] = 'Vous n\'avez pas confirmé le ' . $key;
        }

        return $this;
    }

    /**
     * Renvoie la valeur d'un champs.
     *
     * @param $key
     *
     * @return mixed|null
     */
    private function getValue($key)
    {
        if (!array_key_exists($key, $this->params)) {
            return null;
        }

        return $this->params[$key];
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param Database $database
     *
     * @return self
     */
    public function setDatabase(Database $database): self
    {
        $this->database = $database;

        return $this;
    }

    public function numeric(string $key): self
    {
        $value = $this->getValue($key);
        if (!is_numeric($value)) {
            $this->errors[$key] = 'Le prix ne semble pas valide';
        }

        return $this;
    }
}
