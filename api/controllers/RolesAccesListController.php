<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\AccessList;

/**
 * Class RolesController
 *
 * @package Gewaer\Api\Controllers
 *
 * @property Users $userData
 * @property Request $request
 * @property Config $config
 * @property \Baka\Mail\Message $mail
 * @property Apps $app
 *
 */
class RolesAccesListController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = [];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = [];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new AccessList();

        //get the list of roes for the systema + my company
        $this->additionalSearchFields = [
            ['is_deleted', ':', 0],
            ['apps_id', ':', $this->app->getId()],
        ];
    }
}
