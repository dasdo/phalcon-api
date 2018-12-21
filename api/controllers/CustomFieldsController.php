<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\CustomFields;

/**
 * Class LanguagesController
 *
 * @package Gewaer\Api\Controllers
 *
 */
class CustomFieldsController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['users_id', 'companies_id', 'apps_id', 'name', 'modules_id', 'fields_type_id'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['users_id', 'companies_id', 'apps_id', 'name', 'modules_id', 'fields_type_id'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new CustomFields();
    }
}
