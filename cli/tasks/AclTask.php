<?php

namespace Gewaer\Cli\Tasks;

use Phalcon\Cli\Task as PhTask;

/**
 * Class AclTask
 *
 * @package Gewaer\Cli\Tasks;
 *
 * @property \Gewaer\Acl\Manager $acl
 */
class AclTask extends PhTask
{
    /**
     * Create the default roles of the system
     *
     * @return void
     */
    public function mainAction()
    {
        $this->acl->addRole('Default.Admins');
        $this->acl->addRole('Default.Agents');
        $this->acl->addRole('Default.Users');

        $this->acl->addResource('Default.Users', ['read', 'list', 'create', 'edit', 'delete']);
        $this->acl->allow('Admins', 'Default.Users', ['read', 'list', 'create']);
        $this->acl->deny('Admins', 'Default.Users', ['edit', 'delete']);
    }

    /**
     * Default roles for the crm system
     *
     * @return void
     */
    public function crmAction()
    {
        $this->acl->addRole('CRM.Users');
        $this->acl->addResource('CRM.Users', ['read', 'list', 'create', 'edit', 'delete']);
        $this->acl->allow('Users', 'CRM.Users', ['read', 'list', 'create']);
        $this->acl->deny('Users', 'CRM.Users', ['edit', 'delete']);
    }
}
