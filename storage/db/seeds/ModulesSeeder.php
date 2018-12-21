<?php

use Phinx\Seed\AbstractSeed;

class ModulesSeeder extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'apps_id' => 1,
                'name' => 'example_module',
                'created_at' => date('Y-m-d H:m:s'),
            ],
            [
                'apps_id' => 1,
                'name' => 'companies',
                'created_at' => date('Y-m-d H:m:s'),
            ],
        ];

        $posts = $this->table('modules');
        $posts->insert($data)
              ->save();
    }
}
