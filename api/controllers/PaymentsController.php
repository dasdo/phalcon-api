<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Phalcon\Cashier\Traits\StripeWebhookHandlersTrait;
use Phalcon\Http\Response;
use Gewaer\Models\Users;
use Gewaer\Models\EmailTemplates;
use Phalcon\Di;

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
     * Stripe Webhook Handlers
     */
    use StripeWebhookHandlersTrait;

    /**
     * Handle stripe webhoook calls
     *
     * @return Response
     */
    public function handleWebhook(): Response
    {
        //we cant processs if we dont find the stripe header
        if (!$this->request->hasHeader('Stripe-Signature')) {
            throw new Exception('Route not found for this call');
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
            return $this->{$method}($request, $method);
        } else {
            return $this->response(['Missing Method to Handled']);
        }
    }

    /**
     * Handle customer subscription updated.
     *
     * @param  array $payload
     * @return Response
     */
    protected function handleCustomerSubscriptionUpdated(array $payload, string $method): Response
    {
        $user = Users::findFirstByStripeId($payload['data']['object']['customer']);
        if ($user) {
            //We need to send a mail to the user
            $this->sendWebhookResponseEmail($user, $payload, $method);
        }
        return $this->response(['Webhook Handled']);
    }

    /**
     * Handle customer subscription free trial ending.
     *
     * @param  array $payload
     * @return Response
     */
    protected function handleCustomerSubscriptionTrialwillend(array $payload, string $method): Response
    {
        $user = Users::findFirstByStripeId($payload['data']['object']['customer']);
        if ($user) {
            //We need to send a mail to the user
            $this->sendWebhookResponseEmail($user, $payload, $method);
        }
        return $this->response(['Webhook Handled']);
    }

    /**
     * Handle sucessfull payment
     *
     * @todo send email
     * @param array $payload
     * @return Response
     */
    protected function handleChargeSucceeded(array $payload, string $method): Response
    {
        $user = Users::findFirstByStripeId($payload['data']['object']['customer']);
        if ($user) {
            //We need to send a mail to the user
            $this->sendWebhookResponseEmail($user, $payload, $method);
        }
        return $this->response(['Webhook Handled']);
    }

    /**
     * Handle bad payment
     *
     * @todo send email
     * @param array $payload
     * @return Response
     */
    protected function handleChargeFailed(array $payload, string $method) : Response
    {
        $user = Users::findFirstByStripeId($payload['data']['object']['customer']);
        if ($user) {
            //We need to send a mail to the user
            $this->sendWebhookResponseEmail($user, $payload, $method);
        }
        return $this->response(['Webhook Handled']);
    }

    /**
     * Handle pending payments
     *
     * @todo send email
     * @param array $payload
     * @return Response
     */
    protected function handleChargePending(array $payload, string $method) : Response
    {
        $user = Users::findFirstByStripeId($payload['data']['object']['customer']);
        if ($user) {
            //We need to send a mail to the user
            $this->sendWebhookResponseEmail($user, $payload, $method);
        }
        return $this->response(['Webhook Handled']);
    }

    /**
     * Send webhook related emails to user
     * @param Users $user
     * @param array $payload
     * @param string $method
     * @return void
     */
    protected function sendWebhookResponseEmail(Users $user, array $payload, string $method): void
    {
        switch ($method) {
            case 'handleCustomerSubscriptionTrialwillend':
                $templateName = 'users-trial-end';
                break;
            case 'handleCustomerSubscriptionUpdated':
                $templateName = 'users-subscription-updated';
                break;

            case 'handleChargeSucceeded':
                $templateName = 'users-charge-success';
                break;

            case 'handleChargeFailed':
                $templateName = 'users-charge-failed';
                break;

            case 'handleChargePending':
                $templateName = 'users-charge-pending';
                break;

            default:
                break;
        }

        //Search for actual template by templateName
        $emailTemplate = EmailTemplates::getByName($templateName);

        Di::getDefault()->getMail()
            ->to($user->email)
            ->subject('Canvas Payments and Subscriptions')
            ->content($emailTemplate->template)
            ->sendNow();
    }
}
