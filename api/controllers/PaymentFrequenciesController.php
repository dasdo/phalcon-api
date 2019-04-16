<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\PaymentFrequencies;

/**
 * Class LanguagesController
 *
 * @package Gewaer\Api\Controllers
 *
 */
class PaymentFrequenciesController extends BaseController
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
        $this->model = new PaymentFrequencies();
        $this->additionalSearchFields = [
            ['is_deleted', ':', '0'],
        ];
    }
}
