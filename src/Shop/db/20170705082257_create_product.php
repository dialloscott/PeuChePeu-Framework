<?php

use Phinx\Migration\AbstractMigration;

class CreateProduct extends AbstractMigration
{
    public function change()
    {
        $this->table('products')
            ->addColumn('name', 'string')
            ->addColumn('description', 'text', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG])
            ->addColumn('price', 'float')
            ->addColumn('image', 'string')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->create();
    }
}
