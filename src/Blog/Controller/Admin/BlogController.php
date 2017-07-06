<?php

namespace App\Blog\Controller\Admin;

use App\Admin\CrudController;
use App\Blog\PostUpload;
use App\Blog\Table\CategoriesTable;
use App\Blog\Table\PostTable;
use Framework\Database\Database;
use Framework\Validator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class BlogController extends CrudController
{
    protected $namespace = 'blog';

    protected $files = ['image'];

    /**
     * @var CategoriesTable
     */
    private $categoriesTable;

    public function __construct(
        ContainerInterface $container,
        PostTable $table,
        PostUpload $uploader = null,
        CategoriesTable $categoriesTable
    ) {
        parent::__construct($container, $table, $uploader);
        $this->categoriesTable = $categoriesTable;
    }

    public function preForm(ServerRequestInterface $request)
    {
        return [
            'categories' => $this->categoriesTable->findList('name')
        ];
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    protected function getParams(ServerRequestInterface $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'created_at', 'slug', 'category_id'], true);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Valide les données.
     *
     * @param ServerRequestInterface $request
     * @param Database               $databasea
     * @param int|null               $postId
     *
     * @return array
     */
    protected function validates(ServerRequestInterface $request, Database $databasea, ?int $postId = null): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        $validator = (new Validator($params))
            ->setDatabase($databasea)
            ->required('name', 'content', 'created_at', 'image', 'slug', 'category_id')
            ->slug('slug')
            ->unique('slug', 'posts', $postId)
            ->minLength('name', 4)
            ->minLength('content', 20)
            ->dateTime('created_at')
            ->exists('category_id', 'categories')
            ->extension('image', ['jpg', 'png']);

        if ($request->getMethod() === 'POST') {
            $validator->uploaded('image');
        }

        return $validator->getErrors();
    }
}
