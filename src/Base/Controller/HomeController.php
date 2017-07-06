<?php

namespace App\Base\Controller;

use App\Blog\Table\PostTable;
use Framework\Controller;

class HomeController extends Controller
{
    public function index(PostTable $postTable)
    {
        return $this->render('home', [
            'posts' => $postTable->findLatest()
        ]);
    }
}
