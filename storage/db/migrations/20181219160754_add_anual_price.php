<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddAnualPrice extends AbstractMigration
{
    public function change()
    {
       
        $table = $this->table("apps_plans");
        $table->addColumn('pricing_anual', 'decimal', ['null' => true, 'precision' => 10, 'scale' => 2, 'after' => 'pricing'])->save();
        $table->save();
    }
}
