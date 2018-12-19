<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\CompanyCustomFields;

/**
 * Class LanguagesController
 *
 * @package Gewaer\Api\Controllers
 *
 */
class CompanyCustomFieldsController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['company_id', 'custom_field_id', 'value'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['company_id', 'custom_field_id', 'value'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new CompanyCustomFields();
    }
}
