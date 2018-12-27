<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\CustomFieldsModules;
use Gewaer\CustomFields\CustomFields;
use Phalcon\Http\Response;
use Gewaer\Exception\NotFoundHttpException;
use Gewaer\Exception\PermissionException;

/**
 * Class LanguagesController
 *
 * @package Gewaer\Api\Controllers
 * @property Users $userData
 * @property Apps $app
 *
 */
class CustomFieldsModulesController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['name'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['name'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new CustomFieldsModules();
        $this->model->apps_id = $this->app->getId();

        if (!$this->userData->hasRole('Defaults.Admins')) {
            throw new PermissionException('You dont have permission to run this action ');
        }
    }

    /**
     * Fetch all Custom Fields of a Module
     * @param integer $id
     * @return Response
     */
    public function customFieldsByModulesId(int $id): Response
    {
        //Verify that module exists
        $module = $this->model::findFirst([
            'conditions' => 'id = ?0 and apps_id = ?1 and is_deleted = 0',
            'bind' => [$id, $this->app->getId()]
        ]);

        if (!is_object($module)) {
            throw new NotFoundHttpException('Module not found');
        }

        //List all Custom Fields by module_id, apps and companies
        $customFields = CustomFields::find([
            'conditions' => 'companies_id = ?0 and apps_id = ?1 and is_deleted = 0',
            'bind' => [$this->userData->default_company, $this->app->getId()]
        ]);

        if (!is_object($customFields)) {
            throw new NotFoundHttpException('Custom Fields not found');
        }

        return $this->response($customFields);
    }
}