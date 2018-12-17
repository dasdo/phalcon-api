<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class SubscriptionPlanAcl extends AbstractMigration
{
    public function change()
    {
        $table = $this->table("users");
        $table->addColumn('stripe_id', 'string', ['null' => true, 'limit' => 100, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'banned'])->save();
        $table->addColumn('card_brand', 'string', ['null' => true, 'limit' => 50, 'collation' => "utf8_general_ci", 'encoding' => "utf8", 'after' => 'stripe_id'])->save();
        $table->addColumn('card_last_four', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'card_brand'])->save();
        $table->addColumn('trial_ends_at', 'datetime', ['null' => true, 'after' => 'card_last_four'])->save();

        $this->table("users")->changeColumn('created_at', 'datetime', ['null' => true, 'after' => 'trial_ends_at'])->update();
        $this->table("users")->changeColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->update();
        $this->table("users")->changeColumn('is_deleted', 'integer', ['null' => false, 'default' => "0", 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'updated_at'])->update();
        $table->save();

        $table = $this->table("subscriptions");
        $table->addColumn('apps_plans_id', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'apps_id'])->save();
        $this->table("subscriptions")->changeColumn('name', 'string', ['null' => false, 'limit' => 250, 'collation' => "utf8_unicode_ci", 'encoding' => "utf8", 'after' => 'apps_plans_id'])->update();
        $this->table("subscriptions")->changeColumn('stripe_id', 'string', ['null' => false, 'limit' => 250, 'collation' => "utf8_unicode_ci", 'encoding' => "utf8", 'after' => 'name'])->update();
        $this->table("subscriptions")->changeColumn('stripe_plan', 'string', ['null' => false, 'limit' => 250, 'collation' => "utf8_unicode_ci", 'encoding' => "utf8", 'after' => 'stripe_id'])->update();
        $this->table("subscriptions")->changeColumn('quantity', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'stripe_plan'])->update();
        $this->table("subscriptions")->changeColumn('trial_ends_at', 'timestamp', ['null' => true, 'after' => 'quantity'])->update();
        $this->table("subscriptions")->changeColumn('ends_at', 'timestamp', ['null' => true, 'after' => 'trial_ends_at'])->update();
        $this->table("subscriptions")->changeColumn('created_at', 'datetime', ['null' => false, 'after' => 'ends_at'])->update();
        $this->table("subscriptions")->changeColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->update();
        $this->table("subscriptions")->changeColumn('is_deleted', 'integer', ['null' => false, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->update();
        $table->save();

        $table = $this->table("apps_plans_settings");
        $table->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->save();
        $this->table("apps_plans_settings")->changeColumn('is_deleted', 'boolean', ['null' => true, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->update();
        $table->save();

        if ($this->table('apps_plans_settings')->hasColumn('updadate_at')) {
            $this->table("apps_plans_settings")->removeColumn('updadate_at')->update();
        }

        $table = $this->table("company_branches");
        $table->addColumn('is_default', 'boolean', ['null' => true, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'description'])->save();
        $this->table("company_branches")->changeColumn('created_at', 'datetime', ['null' => false, 'after' => 'is_default'])->update();
        $this->table("company_branches")->changeColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->update();
        $this->table("company_branches")->changeColumn('is_deleted', 'boolean', ['null' => true, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->update();
        $table->save();

        $table = $this->table("system_modules", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_mysql500_ci", 'comment' => "list of modules , user can interact on each of the diff apps", 'row_format' => "Dynamic"]);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10])->save();
        $table->addColumn('name', 'string', ['null' => false, 'limit' => 100, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'id'])->save();
        $table->addColumn('slug', 'string', ['null' => false, 'limit' => 100, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'name'])->save();
        $table->addColumn('model_name', 'string', ['null' => false, 'limit' => 100, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'slug'])->save();
        $table->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'model_name'])->save();
        $table->addColumn('parents_id', 'integer', ['null' => true, 'default' => "0", 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'apps_id'])->save();
        $table->addColumn('menu_order', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'parents_id'])->save();
        $table->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'menu_order'])->save();
        $table->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->save();
        $table->addColumn('is_deleted', 'boolean', ['null' => true, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->save();
        $table->save();

        $table = $this->table("user_company_apps_activities", ['id' => false, 'primary_key' => ["company_id", "company_branches_id", "apps_id", "key"], 'engine' => "InnoDB", 'encoding' => "utf8", 'collation' => "utf8_general_mysql500_ci", 'comment' => "what are the uses doing in the current app they are using?", 'row_format' => "Dynamic"]);
        $table->addColumn('company_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10])->save();
        $table->addColumn('company_branches_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'company_id'])->save();
        $table->addColumn('apps_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'company_branches_id'])->save();
        $table->addColumn('key', 'string', ['null' => false, 'limit' => 50, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'apps_id'])->save();
        $table->addColumn('value', 'string', ['null' => false, 'limit' => 255, 'collation' => "utf8_general_mysql500_ci", 'encoding' => "utf8", 'after' => 'key'])->save();
        $table->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'value'])->save();
        $table->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->save();
        $table->addColumn('is_deleted', 'boolean', ['null' => true, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->save();
        $table->save();

        $table = $this->table("email_templates", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8mb4", 'collation' => "utf8mb4_unicode_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])->save();
        $table->addColumn('users_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'id'])->save();
        $table->addColumn('company_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'users_id'])->save();
        $table->addColumn('app_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'company_id'])->save();
        $table->addColumn('name', 'string', ['null' => false, 'limit' => 64, 'collation' => "utf8mb4_unicode_ci", 'encoding' => "utf8mb4", 'after' => 'app_id'])->save();
        $table->addColumn('template', 'text', ['null' => false, 'limit' => MysqlAdapter::TEXT_LONG, 'collation' => "utf8mb4_unicode_ci", 'encoding' => "utf8mb4", 'after' => 'name'])->save();
        $table->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'template'])->save();
        $table->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->save();
        $table->addColumn('is_deleted', 'integer', ['null' => false, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->save();
        $table->save();

        $table = $this->table("email_templates");
        if ($table->hasIndex("company_id_app_id_name")) {
            $table->removeIndexByName("company_id_app_id_name")->save();
        }

        $table = $this->table("email_templates");
        $table->addIndex(['company_id', 'app_id', 'name'], ['name' => "company_id_app_id_name", 'unique' => true])->save();
        $table = $this->table("users_invite", ['id' => false, 'primary_key' => ["id"], 'engine' => "InnoDB", 'encoding' => "utf8mb4", 'collation' => "utf8mb4_unicode_ci", 'comment' => "", 'row_format' => "Dynamic"]);
        $table->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])->save();
        $table->addColumn('invite_hash', 'string', ['null' => false, 'limit' => 200, 'collation' => "utf8mb4_unicode_ci", 'encoding' => "utf8mb4", 'after' => 'id'])->save();
        $table->addColumn('company_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'invite_hash'])->save();
        $table->addColumn('role_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'company_id'])->save();
        $table->addColumn('app_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'role_id'])->save();
        $table->addColumn('email', 'string', ['null' => false, 'limit' => 64, 'collation' => "utf8mb4_unicode_ci", 'encoding' => "utf8mb4", 'after' => 'app_id'])->save();
        $table->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'email'])->save();
        $table->addColumn('updated_at', 'datetime', ['null' => true, 'after' => 'created_at'])->save();
        $table->addColumn('is_deleted', 'integer', ['null' => false, 'default' => "0", 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'updated_at'])->save();
        $table->save();

        $table = $this->table("suscriptions");
        if ($table->hasIndex("PRIMARY")) {
            $table->removeIndexByName("PRIMARY")->save();
        }
        $table = $this->table("suscriptions");
        if ($table->hasIndex("company_id")) {
            $table->removeIndexByName("company_id")->save();
        }
        $this->table("suscriptions")->drop()->save();
    }
}
