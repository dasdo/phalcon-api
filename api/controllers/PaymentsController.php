<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Phalcon\Http\Response;
use Gewaer\Models\Users;
use Gewaer\Exception\NotFoundHttpException;
use Gewaer\Traits\EmailTrait;
use Datetime;

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

    /**
     * Handle customer subscription updated.
     *
     * @param  array $payload
     * @return Response
     */
    protected function handleCustomerSubscriptionUpdated(array $payload): Response
    {
        $user = Users::findFirstByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $subject = "{$user->firstname} {$user->lastname} Updated Subscription";
            $content = "Dear user {$user->firstname} {$user->lastname}, your subscription has been updated.";

            $template = [
                        'subject' => $subject,
                        'content' => $content
                    ];
            //We need to send a mail to the user
            if (!defined('API_TESTS')) {
                $this->sendWebhookEmail($user->email, $template);
            }
        }
        return $this->response(['Webhook Handled']);
    }

    /**
     * Handle a cancelled customer from a Stripe subscription.
     *
     * @param  array  $payload
     * @return Response
     */
    protected function handleCustomerSubscriptionDeleted(array $payload): Response
    {
        $user = Users::findFirstByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $subscription = $user->getAllSubscriptions('stripe_id =' . $payload['data']['object']['id']);

            if (is_object($subscription)) {
                $subscription->markAsCancelled();
            }
        }
        return $this->response(['Webhook Handled']);
    }

    /**
     * Handle customer subscription free trial ending.
     *
     * @param  array $payload
     * @return Response
     */
    protected function handleCustomerSubscriptionTrialwillend(array $payload): Response
    {
        $trialEndDate = new Datetime();
        $trialEndDate->setTimestamp((int)$payload['data']['object']['trial_end']);
        $formatedEndDate = $trialEndDate->format('Y-m-d H:i:s');

        $user = Users::findFirstByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $subject = "{$user->firstname} {$user->lastname} Free Trial Ending";
            $content = "Dear user {$user->firstname} {$user->lastname}, your free trial is coming to an end on {$formatedEndDate}.Please choose one of our available subscriptions. Thank you";

            $template = [
                'subject' => $subject,
                'content' => $content
            ];
            //We need to send a mail to the user
            if (!defined('API_TESTS')) {
                $this->sendWebhookEmail($user->email, $template);
            }
        }
        return $this->response(['Webhook Handled']);
    }

    /**
     * Handle customer updated.
     *
     * @param  array $payload
     * @return Response
     */
    protected function handleCustomerUpdated(array $payload): Response
    {
        if ($user = Users::findFirstByStripeId($payload['data']['object']['id'])) {
            $user->updateCardFromStripe();
        }
        return $this->response(['Webhook Handled']);
    }

    /**
     * Handle customer source deleted.
     *
     * @param  array $payload
     * @return Response
     */
    protected function handleCustomerSourceDeleted(array $payload) : Response
    {
        if ($user = Users::findFirstByStripeId($payload['data']['object']['customer'])) {
            $user->updateCardFromStripe();
        }
        return $this->response(['Webhook Handled']);
    }

    /**
     * Handle deleted customer.
     *
     * @param  array $payload
     * @return Response
     */
    protected function handleCustomerDeleted(array $payload) : Response
    {
        $user = Users::findFirstByStripeId($payload['data']['object']['id']);
        if ($user) {
            foreach ($user->subscriptions as $subscription) {
                $subscription->skipTrial()->markAsCancelled();
            }

            $user->stripe_id = null;
            $user->trial_ends_at = null;
            $user->card_brand = null;
            $user->card_last_four = null;
            $user->update();
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
    protected function handleChargeSucceeded(array $payload): Response
    {
        $user = Users::findFirstByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $subject = "{$user->firstname} {$user->lastname} Successful Payment";
            $content = "Dear user {$user->firstname} {$user->lastname}, your subscription payment of {$payload['data']['object']['amount']} was successful. Thank you";

            $template = [
                'subject' => $subject,
                'content' => $content
            ];
            //We need to send a mail to the user
            if (!defined('API_TESTS')) {
                $this->sendWebhookEmail($user->email, $template);
            }
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
    protected function handleChargeFailed(array $payload) : Response
    {
        $user = Users::findFirstByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $subject = "{$user->firstname} {$user->lastname} Failed Payment";
            $content = "Dear user {$user->firstname} {$user->lastname}, your subscription presents a failed payment of the amount of {$payload['data']['object']['amount']}. Please check card expiration date";

            $template = [
                'subject' => $subject,
                'content' => $content
            ];
            //We need to send a mail to the user
            if (!defined('API_TESTS')) {
                $this->sendWebhookEmail($user->email, $template);
            }
        }
        return $this->response(['Webhook Handled']);
    }

    /**
     * Handle looking for refund
     *
     * @todo send email
     * @param array $payload
     * @return Response
     */
    protected function handleChargeDisputeCreated(array $payload) : Response
    {
        return $this->response(['Webhook Handled']);
    }

    /**
     * Handle pending payments
     *
     * @todo send email
     * @param array $payload
     * @return Response
     */
    protected function handleChargePending(array $payload) : Response
    {
        $user = Users::findFirstByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $subject = "{$user->firstname} {$user->lastname} Pending Payment";
            $content = "Dear user {$user->firstname} {$user->lastname}, your subscription presents a pending payment of {$payload['data']['object']['amount']}";

            $template = [
                'subject' => $subject,
                'content' => $content
            ];
            //We need to send a mail to the user
            if (!defined('API_TESTS')) {
                $this->sendWebhookEmail($user->email, $template);
            }
        }
        return $this->response(['Webhook Handled']);
    }
}
