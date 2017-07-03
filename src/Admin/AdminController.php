<?php

namespace App\Admin;

use Framework\Controller;

class AdminController extends Controller
{
    public function index(AdminWidgets $adminWidgets)
    {
        return $this->render('@admin/index', ['widgets' => $adminWidgets->getWidgets()]);
    }
}
