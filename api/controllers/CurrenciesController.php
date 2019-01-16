<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\Currencies;

/**
 * Class LanguagesController
 *
 * @package Gewaer\Api\Controllers
 *
 */
class CurrenciesController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['country', 'currency', 'code', 'symbol'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['country', 'currency', 'code', 'symbol'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new Currencies();
        $this->additionalSearchFields = [
            ['is_deleted', ':', '0'],
        ];
    }
}
