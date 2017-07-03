<?php

namespace App\Blog\Table;

use Framework\Database\Table;

class CategoriesTable extends Table
{
    const TABLE = 'categories';

    public function findBySlug($slug)
    {
        return $this->getDatabase()->fetch('SELECT * FROM categories WHERE slug = ?', [$slug]);
    }
}
