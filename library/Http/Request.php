<?php

declare(strict_types=1);

namespace Gewaer\Http;

use Phalcon\Http\Request as PhRequest;
use Gewaer\Traits\RequestJwtTrait;

class Request extends PhRequest
{
    use RequestJwtTrait;
}
