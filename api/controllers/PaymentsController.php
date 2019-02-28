<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Traits\StripeWebhookHandlersTrait;

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
}
