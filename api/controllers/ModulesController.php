<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\Modules;

/**
 * Class LanguagesController
 *
 * @package Gewaer\Api\Controllers
 *
 */
class ModulesController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['apps_id', 'name'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['apps_id', 'name'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new Modules();
    }
}
