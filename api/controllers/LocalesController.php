<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\Locales;

/**
 * Class LanguagesController
 *
 * @package Gewaer\Api\Controllers
 *
 */
class LocalesController extends BaseController
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
        $this->model = new Locales();
        $this->additionalSearchFields = [
            ['is_deleted', ':', '0'],
        ];
    }
}
