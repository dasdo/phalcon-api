<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\Webhooks;

/**
 * Class LanguagesController
 *
 * @package Gewaer\Api\Controllers
 *
 * @property Users $userData
 * @property Request $request
 * @property Config $config
 * @property Apps $app
 *
 */
class WebhooksController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['system_modules_id', 'apps_id', 'name', 'description', 'action', 'format'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['system_modules_id', 'apps_id', 'name', 'description', 'action', 'format'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new Webhooks();
        $this->additionalSearchFields = [
            ['is_deleted', ':', '0'],
            ['apps_id', ':', $this->app->getId()],
        ];
    }
}
