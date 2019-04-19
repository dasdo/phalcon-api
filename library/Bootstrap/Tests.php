<?php

namespace Gewaer\Bootstrap;

use Dmkit\Phalcon\Auth\Middleware\Micro as AuthMicro;

class Tests extends Api
{
    /**
     * Run the application
     *
     * @return mixed
     */
    public function run()
    {
        return $this->application;
    }
}
