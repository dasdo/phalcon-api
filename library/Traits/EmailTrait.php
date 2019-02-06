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
     * @param string $email
     * @param array $template
     * @return void
     */
    public static function sendWebhookEmail(string $email, array $template): void
    {
        $subject = $template['subject'];
        $content = $template['content'];
        Di::getDefault()->getMail()
            ->to($email)
            ->subject($subject)
            ->content($content)
            ->sendNow();
    }
}
