<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\UserWebhooks;

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
class UserWebhooksController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['webhooks_id', 'apps_id', 'users_id', 'companies_id', 'url', 'method', 'format'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['webhooks_id', 'apps_id', 'users_id', 'companies_id', 'url', 'method', 'format'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new UserWebhooks();
        $this->additionalSearchFields = [
            ['is_deleted', ':', '0'],
            ['apps_id', ':', $this->$this->app->getId()],
            ['companies_id', ':', '0|' . $this->userData->currentCompanyId()],
        ];
    }
}
