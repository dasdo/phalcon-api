<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\FileSystem;
use Gewaer\Traits\FileManagementTrait;

/**
 * Class BaseController
 *
 * @package Gewaer\Api\Controllers
 *
 */
class FilesystemController extends BaseController
{
    use FileManagementTrait;

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
        $this->model = new FileSystem();
        $this->model->users_id = $this->userData->getId();
        $this->model->companies_id = $this->userData->currentCompanyId();

        $this->additionalSearchFields = [
            ['is_deleted', ':', '0'],
            ['companies_id', ':', $this->userData->currentCompanyId()],
            ['apps_id', ':', $this->app->getId()]
        ];
    }
}
