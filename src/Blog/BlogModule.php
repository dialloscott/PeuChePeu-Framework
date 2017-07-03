<?php

namespace App\Blog;

use App\Auth\Middleware\RoleMiddleware;
use App\Blog\Controller\Admin\BlogController as AdminBlogController;
use App\Blog\Controller\Admin\CategoriesController as AdminCategoriesController;
use App\Blog\Controller\BlogController;
use Framework\App;
use Framework\Module;
use Framework\View\ViewInterface;
use Psr\Container\ContainerInterface;

class BlogModule extends Module
{
    public const MIGRATIONS = __DIR__ . '/db/migrations';
    public const SEEDS = __DIR__ . '/db/seeds';
    public const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(
        App $app,
        RoleMiddleware $middleware,
        ViewInterface $view,
        ContainerInterface $container
    ) {
        // Ajout du dossier des vues
        $view->addPath(__DIR__ . '/views', 'blog');

        // Gestion des routes
        $app->get('/blog', [BlogController::class, 'index'])->setName('blog.index');
        $app->get('/blog/{slug}', [BlogController::class, 'show'])->setName('blog.show');
        $app->get('/blog/category/{slug}', [BlogController::class, 'category'])->setName('blog.category');

        // Pour le backend
        if ($container->has('admin.middleware')) {
            $app->group($container->get('admin.prefix'), function () {
                // Gestion des articles
                $this->get('/blog', [AdminBlogController::class, 'index'])->setName('blog.admin.index');
                $this
                    ->map(['GET', 'POST'], '/blog/new', [AdminBlogController::class, 'create'])
                    ->setName('blog.admin.create');
                $this
                    ->map(['GET', 'PUT'], '/blog/{id:[0-9]+}', [AdminBlogController::class, 'edit'])
                    ->setName('blog.admin.edit');
                $this
                    ->delete('/blog/{id:[0-9]+}', [AdminBlogController::class, 'destroy'])
                    ->setName('blog.admin.destroy');

                // Gestion des categories
                $this
                    ->get('/categories', [AdminCategoriesController::class, 'index'])
                    ->setName('blog.admin.category.index');
                $this
                    ->map(['GET', 'POST'], '/categories/new', [AdminCategoriesController::class, 'create'])
                    ->setName('blog.admin.category.create');
                $this
                    ->map(['GET', 'PUT'], '/categories/{id:[0-9]+}', [AdminCategoriesController::class, 'edit'])
                    ->setName('blog.admin.category.edit');
                $this
                    ->delete('/categories/{id:[0-9]+}', [AdminCategoriesController::class, 'destroy'])
                    ->setName('blog.admin.category.destroy');
            })->add($container->get('admin.middleware'));
        }

        // Gestion du widget
        if ($container->has('admin.widgets')) {
            $container->get('admin.widgets')->add($container->get(BlogWidget::class));
        }
    }
}
