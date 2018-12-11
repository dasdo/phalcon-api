<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\UsersInvite;

/**
 * Class LanguagesController
 *
 * @package Gewaer\Api\Controllers
 *
 */
class UsersInviteController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['invite_hash', 'company_id', 'role_id', 'app_id', 'email'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['invite_hash', 'company_id', 'role_id', 'app_id', 'email'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new UsersInvite();
    }
}
