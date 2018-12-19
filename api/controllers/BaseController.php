<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Baka\Http\Rest\CrudExtendedController;

/**
 * Class BaseController
 *
 * @package Gewaer\Api\Controllers
 *
 */
abstract class BaseController extends CrudExtendedController
{
    /**
     * activate softdelete
     * 
     * @var int
     */
    public $softDelete = 1;
}
