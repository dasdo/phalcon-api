<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\EmailTemplatesVariables;

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
class EmailTemplatesVariablesController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['companies_id', 'apps_id', 'system_modules_id', 'users_id', 'email_templates_id', 'value'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['companies_id', 'apps_id', 'system_modules_id', 'users_id', 'email_templates_id', 'value'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new EmailTemplatesVariables();
        $this->additionalSearchFields = [
            ['is_deleted', ':', '0'],
            ['companies_id', ':', '0|' . $this->userData->currentCompanyId()],
        ];
    }
}
