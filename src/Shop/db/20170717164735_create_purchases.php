<?php

use Phinx\Migration\AbstractMigration;

class CreatePurchases extends AbstractMigration
{
    public function change()
    {
        $this->table('purchases')
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'SET_NULL'
            ])
            ->addColumn('product_id', 'integer', ['null' => true])
            ->addForeignKey('product_id', 'products', 'id', [
                'delete' => 'SET_NULL'
            ])
            ->addColumn('price', 'float')
            ->addColumn('vat', 'float')
            ->addColumn('stripe_id', 'string')
            ->addColumn('created_at', 'datetime')
            ->create();
    }
}
