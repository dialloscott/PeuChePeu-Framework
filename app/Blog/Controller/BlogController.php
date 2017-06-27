<?php

namespace App\Blog\Controller;

use App\Blog\Table\CategoriesTable;
use App\Blog\Table\PostTable;
use Core\Controller;
use Core\Database\NoRecordException;
use Core\View\ViewInterface;
use Slim\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request, PostTable $postTable, CategoriesTable $categoriesTable)
    {
        $page = $request->getParam('page', 1);
        $posts = $postTable->findPaginated(12, $page);
        $categories = $categoriesTable->findall();

        return $this->render('@blog/index', compact('posts', 'page', 'categories'));
    }

    public function category(string $slug, Request $request, PostTable $postTable, CategoriesTable $categoriesTable)
    {
        $category = $categoriesTable->findBySlug($slug);
        if (empty($category)) {
            throw new NoRecordException();
        }
        $page = $request->getParam('page', 1);
        $posts = $postTable->findPaginatedByCategory(12, $page, $slug);
        $categories = $categoriesTable->findall();

        return $this->render('@blog/category', compact('category', 'posts', 'page', 'categories'));
    }

    public function show(string $slug, PostTable $postTable, ViewInterface $view)
    {
        $post = $postTable->findBySlug($slug);

        return $view->render('@blog/show', compact('post'));
    }
}
