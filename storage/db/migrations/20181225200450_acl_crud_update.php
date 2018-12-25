<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AclCrudUpdate extends AbstractMigration
{
    public function change()
    {
        $this->table('access_list', [
                'id' => false,
                'primary_key' => ['roles_id', 'resources_name', 'access_name', 'apps_id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_520_ci',
                'comment' => '',
                'row_format' => 'Dynamic',
            ])
            ->addColumn('roles_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'precision' => '10',
                'after' => 'roles_name',
            ])
            ->save();

        $this->table('resources_accesses', [
                'id' => false,
                'primary_key' => ['resources_id', 'resources_name', 'access_name', 'apps_id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_520_ci',
                'comment' => '',
                'row_format' => 'Dynamic',
            ])
            ->addColumn('resources_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'precision' => '10',
            ])
            ->save();

        $this->table('roles_inherits', [
                'id' => false,
                'primary_key' => ['roles_id', 'roles_inherit'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_520_ci',
                'comment' => '',
                'row_format' => 'Dynamic',
            ])
            ->addColumn('roles_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'precision' => '10',
            ])
        ->changeColumn('roles_inherit', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'precision' => '10',
                'after' => 'roles_id',
            ])
            ->save();
        if ($this->table('roles_inherits')->hasColumn('roles_name')) {
            $this->table('roles_inherits')->removeColumn('roles_name')->update();
        }
    }
}
