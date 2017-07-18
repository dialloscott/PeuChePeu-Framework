<?php

namespace App\Shop\Table;

use Framework\Database\Table;

class PurchaseTable extends Table
{
    public const TABLE = 'purchases';

    public function findForUser(int $id): array
    {
        return $this->database->fetchAll('
            SELECT purchases.*, products.name as product_name, products.id as product_id
            FROM purchases
            LEFT JOIN products ON products.id = purchases.product_id
            WHERE purchases.user_id = ?
        ', [$id]);
    }
}
