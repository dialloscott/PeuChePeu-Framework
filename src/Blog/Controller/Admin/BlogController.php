<?php

namespace App\Blog\Controller\Admin;

use App\Blog\PostUpload;
use App\Blog\Table\CategoriesTable;
use App\Blog\Table\PostTable;
use Framework\Controller;
use Framework\Database\Database;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request, PostTable $postTable): string
    {
        $page = $request->getParam('page', 1);
        $posts = $postTable->findPaginated(10, $page);

        return $this->render('@blog/admin/index', compact('posts'));
    }

    public function create(
        ServerRequestInterface $request,
        PostTable $postTable,
        CategoriesTable $categoriesTable,
        PostUpload $uploader
    ) {
        $post = [
            'created_at' => date('Y-m-d H:i:s')
        ];
        $categories = $categoriesTable->findList('name');

        if ($request->getMethod() === 'POST') {
            $post = $this->getParams($request);
            $errors = $this->validates($request, $postTable->getDatabase());

            if (empty($errors)) {
                $post['image'] = $uploader->upload($request->getUploadedFiles()['image']);
                $postTable->create($post);
                $this->flash('success', "L'article a bien été créé");

                return $this->redirect('blog.admin.index');
            }
        }

        return $this->render('@blog/admin/create', compact('post', 'errors', 'categories'));
    }

    public function edit(
        int $id,
        ServerRequestInterface $request,
        PostTable $postTable,
        CategoriesTable $categoriesTable,
        PostUpload $uploader
    ) {
        $post = $postTable->findOrFail($id);
        $categories = $categoriesTable->findList('name');

        if ($request->getMethod() === 'PUT') {
            $postEntity = $post;
            $post = $this->getParams($request);
            $errors = $this->validates($request, $postTable->getDatabase(), $id);

            if (empty($errors)) {
                /* @var UploadedFileInterface $file */
                $file = $request->getUploadedFiles()['image'];

                if ($file && $file->getError() === UPLOAD_ERR_OK) {
                    $post['image'] = $uploader->upload($file, $postEntity->image);
                }

                // On met à jour la table
                $postTable->update($id, $post);
                $this->flash('success', "L'article a bien été modifié");

                return $this->redirect('blog.admin.index');
            }
        }

        return $this->render('@blog/admin/edit', compact('post', 'errors', 'categories'));
    }

    public function destroy(int $id, PostTable $postTable, PostUpload $uploader): ResponseInterface
    {
        $post = $postTable->findOrFail($id);
        $uploader->delete($post->image);
        $postTable->delete($id);
        $this->flash('success', "L'article a bien été supprimé");

        return $this->redirect('blog.admin.index');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    private function getParams(ServerRequestInterface $request): array
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
    private function validates(ServerRequestInterface $request, Database $databasea, ?int $postId = null): array
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
