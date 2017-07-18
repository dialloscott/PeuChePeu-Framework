<?php

namespace App\Admin;

use Framework\Controller;
use Framework\Database\Database;
use Framework\Database\Table;
use Framework\Upload;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class CrudController extends Controller
{
    /**
     * @var Table
     */
    protected $table;

    /**
     * Vue / Route namespace.
     *
     * @var string
     */
    protected $namespace;

    /**
     * @var Upload|null
     */
    protected $uploader;

    /**
     * Champs qui sont des fichiers.
     *
     * @var array
     */
    protected $files = [];

    public function __construct(ContainerInterface $container, Table $table, ?Upload $uploader = null)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->table = $table;
        $this->uploader = $uploader;
    }

    public function index(ServerRequestInterface $request): string
    {
        $params = $request->getParsedBody();
        $page = isset($params['page']) ? $params['page'] : 1;
        $items = $this->table->findPaginated(10, $page);

        return $this->render('@' . $this->namespace . '/admin/index', compact('items'));
    }

    public function preForm(ServerRequestInterface $request)
    {
        return [];
    }

    public function create(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $item = $this->getParams($request);
            $item['created_at'] = date('Y-m-d H:i:s');
            $errors = $this->validates($request, $this->table->getDatabase());

            if (empty($errors)) {
                foreach ($this->files as $key) {
                    $item[$key] = $this->uploader->upload($request->getUploadedFiles()[$key]);
                }
                $id = $this->table->create($item);
                $this->postPersist($request, $id);
                $this->flash('success', $this->getSuccessCreateMessage());

                return $this->redirect($this->namespace . '.admin.index');
            }
        }

        return $this->render('@' . $this->namespace . '/admin/create', array_merge(
            compact('item', 'errors'),
            $this->preForm($request)
        ));
    }

    public function edit(int $id, ServerRequestInterface $request)
    {
        $item = $this->table->findOrFail($id);

        if ($request->getMethod() === 'PUT') {
            $itemEntity = $item;
            $item = $this->getParams($request);
            $errors = $this->validates($request, $this->table->getDatabase(), $id);

            if (empty($errors)) {
                /* @var UploadedFileInterface $file */

                foreach ($this->files as $key) {
                    $file = $request->getUploadedFiles()[$key];

                    if ($file && $file->getError() === UPLOAD_ERR_OK) {
                        $item[$key] = $this->uploader->upload($file, $itemEntity->image);
                    }
                }

                // On met à jour la table
                $this->table->update($id, $item);
                $this->postPersist($request, $id);
                $this->flash('success', $this->getSuccessUpdateMessage());

                return $this->redirect($this->namespace . '.admin.index');
            }
        }

        return $this->render('@' . $this->namespace . '/admin/edit', array_merge(
            compact('item', 'errors'),
            $this->preForm($request)
        ));
    }

    public function destroy(int $id): ResponseInterface
    {
        $item = $this->table->findOrFail($id);
        $this->uploader->delete($item->image);
        $this->table->delete($id);
        $this->flash('success', $this->getSuccessDeleteMessage());

        return $this->redirect($this->namespace . '.admin.index');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    protected function getParams(ServerRequestInterface $request): array
    {
        return $request->getParsedBody();
    }

    /**
     * Valide les données.
     *
     * @param ServerRequestInterface $request
     * @param Database               $databasea
     * @param int|null               $id
     *
     * @return array
     */
    protected function validates(ServerRequestInterface $request, Database $databasea, ?int $id = null): array
    {
        return [];
    }

    protected function getSuccessCreateMessage()
    {
        return "L'élément a bien été modifié";
    }

    protected function getSuccessUpdateMessage()
    {
        return "L'élément a bien été modifié";
    }

    protected function getSuccessDeleteMessage()
    {
        return "L'élément a bien été supprimé";
    }

    protected function postPersist(ServerRequestInterface $request, int $id)
    {
    }
}
