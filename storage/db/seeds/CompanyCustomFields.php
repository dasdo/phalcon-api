<?php

use Phinx\Seed\AbstractSeed;

class CompanyCustomFieldsSeeder extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'company_id' => 1,
                'custom_field_id' => 1,
                'name' => 'example_value',
                'created_at' => date('Y-m-d H:m:s'),
            ],
        ];

        $posts = $this->table('company-custom-fields');
        $posts->insert($data)
              ->save();
    }
}
