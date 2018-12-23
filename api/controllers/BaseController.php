<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Baka\Http\Rest\CrudExtendedController;
use Phalcon\Http\Response;

/**
 * Class BaseController
 *
 * @package Gewaer\Api\Controllers
 *
 */
abstract class BaseController extends CrudExtendedController
{
    /**
     * Set JSON response for AJAX, API request
     *
     * @param mixed $content
     * @param integer $statusCode
     * @param string $statusMessage
     *
     * @return \Phalcon\Http\Response
     */
    public function response($content, int $statusCode = 200, string $statusMessage = 'OK'): Response
    {
        $response = [
            'statusCode' => $statusCode,
            'statusMessage' => $statusMessage,
            'content' => $content,
        ];

        // Create a response since it's an ajax
        $response = $this->response;
        $response->setStatusCode($statusCode, $statusMessage);
        //$response->setContentType('application/vnd.api+json', 'UTF-8');
        $response->setJsonContent($content);

        //clean services we need to be fresh on each request
        if (defined('ENGINE') && ENGINE == 'SWOOLE') {
            //$this->db->close();
           
            //remove the userData service
            if ($this->di->has('userData')) {
                $service = $this->di->getService('userData');
                $this->di->remove($service->getName());
            }

            //close db
            if ($this->di->has('db')) {
                $db = $this->di->getService('db');
                $this->db->close();
                $this->di->remove($db->getName());
            }

             $this->di->reset();

        }

        return $response;
    }
}
