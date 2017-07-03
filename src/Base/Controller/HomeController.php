<?php

namespace App\Base\Controller;

use Framework\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->render('home');
    }
}
