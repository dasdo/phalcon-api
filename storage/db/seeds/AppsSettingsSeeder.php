<?php

use Phinx\Seed\AbstractSeed;

class AppsSettingsSeeder extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'apps_id' => 0,
                'name' => 'language',
                'value' => 'EN',
                'created_at' => date('Y-m-d H:m:s'),
            ],
            [
                'apps_id' => 0,
                'name' => 'timezone',
                'value' => 'America/New_York',
                'created_at' => date('Y-m-d H:m:s'),
            ],
            [
                'apps_id' => 0,
                'name' => 'currency',
                'value' => 'USD',
                'created_at' => date('Y-m-d H:m:s'),
            ],
        ];

        $posts = $this->table('apps_settings');
        $posts->insert($data)
              ->save();
    }
}
