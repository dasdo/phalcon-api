<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Phalcon\Http\Response;
use Gewaer\Exception\NotFoundHttpException;
use Gewaer\Traits\EmailTrait;
use Gewaer\Traits\WebhookHandlersTrait;

/**
 * Class PaymentsController
 *
 * Class to handle payment webhook from our cashier library
 *
 * @package Gewaer\Api\Controllers
 * @property Log $log
 *
 */
class PaymentsController extends BaseController
{
    /**
     * Email Trait
     */
    use EmailTrait;

    use WebhookHandlersTrait;

    /**
     * Handle stripe webhoook calls
     *
     * @return Response
     */
    public function handleWebhook(): Response
    {
        //we cant processs if we dont find the stripe header
        if (!defined('API_TESTS')) {
            if (!$this->request->hasHeader('Stripe-Signature')) {
                throw new NotFoundHttpException('Route not found for this call');
            }
        }

        $request = $this->request->getPost();

        if (empty($request)) {
            $request = $this->request->getJsonRawBody(true);
        }
        $type = str_replace('.', '', ucwords(str_replace('_', '', $request['type']), '.'));
        $method = 'handle' . $type;

        $payloadContent = json_encode($request);
        $this->log->info("Webhook Handler Method: {$method} \n");
        $this->log->info("Payload: {$payloadContent} \n");

        if (method_exists($this, $method)) {
            return $this->{$method}($request);
        } else {
            return $this->response(['Missing Method to Handled']);
        }
    }
}
