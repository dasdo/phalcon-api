<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Phalcon\Http\Response;
use Gewaer\Models\Users;
use Carbon\Carbon;
use Gewaer\Exception\NotFoundHttpException;

/**
 * Class PaymentsController
 *
 * Class to handle payment webhook from our cashier library
 *
 * @package Gewaer\Api\Controllers
 *
 */
class PaymentsController extends BaseController
{
    /**
     * Handle stripe webhoook calls
     *
     * @return Response
     */
    public function handleWebhook(): Response
    {
        //we cant processs if we dont find the stripe header
        if (!$this->request->hasHeader('Stripe-Signature')) {
            throw new NotFoundHttpException('Route not found for this call');
        }

        $request = $this->request->getPost();

        if (empty($request)) {
            $request = $this->request->getJsonRawBody(true);
        }
        $value = ucwords(str_replace(['-', '_'], '', str_replace('.', '_', $request['type'])));
        $method = 'handle' . $value;
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
            $data = $payload['data']['object'];
            //get the current subscription they are updating
            $subscription = $user->getAllSubscriptions('stripe_id =' . $data['id']);

            if (is_object($subscription)) {
                // Quantity...
                if (isset($data['quantity'])) {
                    $subscription->quantity = $data['quantity'];
                }
                // Plan...
                if (isset($data['plan']['id'])) {
                    $subscription->stripe_plan = $data['plan']['id'];
                }
                // Trial ending date...
                if (isset($data['trial_end'])) {
                    $trial_ends = Carbon::createFromTimestamp($data['trial_end']);
                    if (!$subscription->trial_ends_at || $subscription->trial_ends_at->ne($trial_ends)) {
                        $subscription->trial_ends_at = $trial_ends;
                    }
                }
                // Cancellation date...
                if (isset($data['cancel_at_period_end']) && $data['cancel_at_period_end']) {
                    $subscription->ends_at = $subscription->onTrial()
                        ? $subscription->trial_ends_at
                        : Carbon::createFromTimestamp($data['current_period_end']);
                }

                $subscription->update();
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
}
