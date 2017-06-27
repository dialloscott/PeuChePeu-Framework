<?php
namespace App\Blog\Controller\Admin;

use App\Blog\Table\CategoriesTable;
use Core\Controller;

class CategoriesController extends Controller {

    public function index (CategoriesTable $categoriesTable) {
        $categories = $categoriesTable->findPaginated();
        return $this->render('@blog/admin/categories/index', compact('categories'));
    }

    public function create () {
        return $this->render('@blog/admin/categories/create');
    }

}