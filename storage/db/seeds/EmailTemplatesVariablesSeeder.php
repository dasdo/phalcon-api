<?php

use Phinx\Seed\AbstractSeed;

class EmailTemplatesVariablesSeeder extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'users_id' => 1,
                'companies_id' => 0,
                'apps_id' => 0,
                'system_modules_id' => 1,
                'email_templates_id' => 1,
                'value' => 'example_content',
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $posts = $this->table('email_templates_variables');
        $posts->insert($data)
              ->save();
    }
}
