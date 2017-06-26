<?php

namespace App\Blog;

use Psr\Http\Message\UploadedFileInterface;

class PostUpload
{
    /**
     * Dossier où déplacer les fichiers.
     *
     * @var string
     */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Gère l'upload d'un fichier pour un article.
     *
     * @param UploadedFileInterface $file
     * @param null|string           $oldImage
     *
     * @return string
     */
    public function upload(UploadedFileInterface $file, ?string $oldImage = null): string
    {
        $this->delete($oldImage);
        if (!file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }
        $filename = $this->addSuffix($file->getClientFilename());
        $file->moveTo($this->getFullPath($filename));

        return $file->getClientFilename();
    }

    /**
     * Supprime l'image uploadée pour un article.
     *
     * @param null|string $filename
     */
    public function delete(?string $filename): void
    {
        if ($filename) {
            $fullpath = $this->getFullPath($filename);
            if (file_exists($fullpath)) {
                unlink($fullpath);
            }
        }
    }

    /**
     * Retourne le chemin absolu pour à partir du nom du fichier.
     *
     * @param string $name
     *
     * @return string
     */
    private function getFullPath(string $name): string
    {
        return $this->path . '/' . $name;
    }

    /**
     * Ajoute un suffix "copy" à la fin du nom du fichier si il existe déjà.
     *
     * @param string $name
     *
     * @return string
     */
    private function addSuffix(string $name): string
    {
        if (file_exists($this->getFullPath($name))) {
            ['filename' => $filename, 'extension' => $extension] = pathinfo($name);

            return $this->addSuffix($filename . '_copy.' . $extension);
        }

        return $name;
    }
}
