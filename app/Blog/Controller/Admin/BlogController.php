<?php

namespace App\Blog\Controller\Admin;

use App\Blog\PostUpload;
use App\Blog\Table\PostTable;
use Core\Controller;
use Core\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;

class BlogController extends Controller
{
    public function index(PostTable $postTable, Request $request): string
    {
        $page = $request->getParam('page', 1);
        $posts = $postTable->findPaginated(10, $page);

        return $this->render('@blog/admin/index', compact('posts'));
    }

    public function create(ServerRequestInterface $request, PostTable $postTable, PostUpload $uploader)
    {
        $post = [
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($request->getMethod() === 'POST') {
            $post = $this->getParams($request);
            $errors = $this->validates($request, $postTable);

            if (empty($errors)) {
                $post['image'] = $uploader->upload($request->getUploadedFiles()['image']);
                $postTable->create($post);
                $this->flash('success', "L'article a bien été créé");

                return $this->redirect('blog.admin.index');
            }
        }

        return $this->render('@blog/admin/create', compact('post', 'errors'));
    }

    public function edit(int $id, ServerRequestInterface $request, PostTable $postTable, PostUpload $uploader)
    {
        $post = $postTable->findOrFail($id);

        if ($request->getMethod() === 'PUT') {
            $postEntity = $post;
            $post = $this->getParams($request);
            $errors = $this->validates($request, $postTable, $id);

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

        return $this->render('@blog/admin/edit', compact('post', 'errors'));
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
            return in_array($key, ['name', 'content', 'created_at', 'slug'], true);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Valide les données.
     *
     * @param ServerRequestInterface    $request
     * @param \App\Blog\Table\PostTable $postTable
     * @param int|null                  $postId
     *
     * @return array|bool
     */
    private function validates(ServerRequestInterface $request, PostTable $postTable, ?int $postId = null): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        $validator = (new Validator($params))
            ->required('name', 'content', 'created_at', 'image', 'slug')
            ->slug('slug')
            ->unique('slug', $postTable, $postId)
            ->minLength('name', 4)
            ->minLength('content', 20)
            ->dateTime('created_at')
            ->extension('image', ['jpg', 'png']);

        if ($request->getMethod() === 'POST') {
            $validator->uploaded('image');
        }

        return $validator->getErrors();
    }
}
