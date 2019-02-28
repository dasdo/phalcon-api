<?php

declare(strict_types=1);

namespace Gewaer\Traits;

use Gewaer\Models\Users;
use Phalcon\Di;

/**
 * Trait EmailTrait
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
trait EmailTrait
{
    /**
     * Send webhook related emails to user
     * @param Users $user
     * @param array $payload
     * @return void
     */
    public static function sendWebhookEmail(Users $user, array $payload): void
    {
        Di::getDefault()->getMail()
            ->to($email)
            ->subject($subject)
            ->content($content)
            ->sendNow();
    }
}
