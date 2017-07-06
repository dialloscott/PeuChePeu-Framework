<?php

namespace App\Shop\Controller;

use App\Admin\CrudController;
use App\Shop\ProductUpload;
use App\Shop\Table\ProductTable;
use Framework\Database\Database;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class AdminProductController extends CrudController
{
    protected $files = ['image'];
    protected $namespace = 'shop';

    public function __construct(ContainerInterface $container, ProductTable $table, ProductUpload $uploader = null)
    {
        parent::__construct($container, $table, $uploader);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    protected function getParams(ServerRequestInterface $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'description', 'image', 'price'], true);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Valide les données.
     *
     * @param ServerRequestInterface $request
     * @param Database               $databasea
     * @param int|null               $productId
     *
     * @return array
     */
    protected function validates(ServerRequestInterface $request, Database $databasea, ?int $productId = null): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        $validator = (new Validator($params))
            ->setDatabase($databasea)
            ->required('name', 'description', 'price')
            ->numeric('price')
            ->minLength('name', 4)
            ->minLength('description', 20)
            ->extension('image', ['jpg', 'png']);

        if ($request->getMethod() === 'POST') {
            $validator->uploaded('image');
        }

        return $validator->getErrors();
    }
}
