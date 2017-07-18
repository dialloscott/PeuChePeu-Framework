<?php

namespace App\Shop\Controller;

use App\Auth\Entity\User;
use App\Shop\Table\PurchaseTable;
use Framework\Controller;

class PurchaseController extends Controller
{
    public function index(User $user, PurchaseTable $purchaseTable)
    {
        $purchases = $purchaseTable->findForUser($user->id);

        return $this->render('@shop/purchases', compact('purchases'));
    }
}
