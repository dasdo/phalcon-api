<?php

declare(strict_types=1);

namespace Gewaer\Traits;

use Gewaer\Models\Users;
use Phalcon\Http\Response;

/**
 * Trait WebhookHandlers
 *
 * @package Gewaer\Traits
 *
 * @property Users $user
 * @property AppsPlans $appPlan
 * @property CompanyBranches $branches
 * @property Companies $company
 * @property UserCompanyApps $app
 * @property \Phalcon\Di $di
 *
 */
trait WebhookHandlersTrait
{
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
            //We need to send a mail to the user
            if (!defined('API_TESTS')) {
                $this->sendWebhookEmail($user, $payload);
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
        $user = Users::findFirstByStripeId($payload['data']['object']['customer']);
        if ($user) {
            //We need to send a mail to the user
            if (!defined('API_TESTS')) {
                $this->sendWebhookEmail($user, $payload);
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
            //We need to send a mail to the user
            if (!defined('API_TESTS')) {
                $this->sendWebhookEmail($user, $payload);
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
            //We need to send a mail to the user
            if (!defined('API_TESTS')) {
                $this->sendWebhookEmail($user, $payload);
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
            //We need to send a mail to the user
            if (!defined('API_TESTS')) {
                $this->sendWebhookEmail($user, $payload);
            }
        }
        return $this->response(['Webhook Handled']);
    }
}
