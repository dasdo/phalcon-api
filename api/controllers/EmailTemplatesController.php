<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\EmailTemplates;

/**
 * Class LanguagesController
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
class EmailTemplatesController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['users_id', 'company_id', 'app_id', 'name', 'template'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['users_id', 'company_id', 'app_id', 'name', 'template'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new EmailTemplates();
        $this->additionalSearchFields = [
            ['is_deleted', ':', 0],
            ['company_id', ':', '(0,' . $this->userData->default_company . ')'],
        ];
    }
}
