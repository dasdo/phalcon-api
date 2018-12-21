<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\CompaniesCustomFields;

/**
 * Class LanguagesController
 *
 * @package Gewaer\Api\Controllers
 *
 */
class CompaniesCustomFieldsController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['companies_id', 'custom_fields_id', 'value'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['companies_id', 'custom_fields_id', 'value'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new CompaniesCustomFields();
    }
}
