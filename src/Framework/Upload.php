<?php

namespace Framework;

use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;

class Upload
{
    /**
     * Dossier où déplacer les fichiers.
     *
     * @var string
     */
    protected $path;

    /**
     * Liste les formats à générer.
     *
     * @var array
     */
    protected $formats = [];

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
        // On supprime l'image précédente
        $this->delete($oldImage);
        // On crée le dossier si il n'existe pas déjà
        if (!file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }

        // On rajoute "_copy" à la fin du fichier (si doublon)
        $filename = $this->addSuffix($file->getClientFilename());

        // On déplace le fichier
        $file->moveTo($this->getFullPath($filename));

        // On génère les différents formats
        foreach ($this->formats as $format => $size) {
            $source = $this->getFullPath($filename);
            $destination = $this->getFullPath($this->getFilenameForFormat($filename, $format));
            $manager = new ImageManager(['driver' => 'gd']);
            [$width, $height] = $size;

            $manager->make($source)->fit($width, $height)->save($destination);
        }

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
            // On supprimé l'image originale
            $fullpath = $this->getFullPath($filename);
            if (file_exists($fullpath)) {
                unlink($fullpath);
            }
            // On supprime les formats
            foreach ($this->formats as $format => $_) {
                $file = $this->getFullPath($this->getFilenameForFormat($filename, $format));
                if (file_exists($file)) {
                    unlink($file);
                }
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

    /**
     * Renvoie le nom du fichier avec le format en suffix.
     *
     * @param string $name
     * @param string $format
     *
     * @return string
     */
    private function getFilenameForFormat(string $name, string $format): string
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($name);

        return $filename . '_' . $format . '.' . $extension;
    }
}
