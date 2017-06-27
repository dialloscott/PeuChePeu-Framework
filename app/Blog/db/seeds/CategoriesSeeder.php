<?php

use Phinx\Seed\AbstractSeed;

class CategoriesSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [];
        for ($i = 0; $i < 10; ++$i) {
            $data[] = [
                'name'       => "Catégorie $i",
                'slug'       => "category-$i",
            ];
        }
        $this->table('categories')
            ->insert($data)
            ->save();
    }
}
