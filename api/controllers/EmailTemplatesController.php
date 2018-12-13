<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\EmailTemplates;

/**
 * Class LanguagesController
 *
 * @package Gewaer\Api\Controllers
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
    }
}
