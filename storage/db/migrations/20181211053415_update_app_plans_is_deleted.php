<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class UpdateAppPlansIsDeleted extends AbstractMigration
{
    public function change()
    {
        $this->table('apps_plans')->changeColumn('is_deleted', 'integer', ['null' => true, 'default' => '0', 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->update();

        //add default languages
        $data = [
            [
                'apps_id' => '1',
                'name' => 'monthly-10-1',
                'description' => 'monthly-10-1',
                'stripe_id' => 'monthly-10-1',
                'stripe_plan' => 'monthly-10-1',
                'pricing' => 10,
                'currency_id' => 1,
                'free_trial_dates' => 14,
                'is_default' => 1
            ],
        ];

        $table = $this->table('apps_plans');
        $table->insert($data)->save();
    }
}
