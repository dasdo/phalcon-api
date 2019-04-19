<?php

declare(strict_types=1);

namespace Gewaer\Http;

use Phalcon\Http\Request as PhRequest;
use Baka\Http\RouterCollection;
use Phalcon\Mvc\Router\Route;
use Gewaer\Contracts\RequestJwtInterface;
use Gewaer\Traits\RequestJwtTrait;

class Request extends PhRequest
{
    use RequestJwtTrait;
}
