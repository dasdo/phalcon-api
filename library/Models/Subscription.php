<?php

namespace Gewaer\Models;

use Phalcon\Cashier\Subscription as PhalconSubscription;
use Gewaer\Exception\ServerErrorHttpException;
use Phalcon\Di;

class Subscription extends PhalconSubscription
{
    public $apps_plans_id = 0;

    /**
     * Initialize
     *
     * @return void
     */
    public function initialize()
    {
        $this->belongsTo('user_id', 'Gewaer\Models\Users', 'id', ['alias' => 'user']);

        $this->belongsTo(
            'company_id',
            'Gewaer\Models\Companies',
            'id',
            ['alias' => 'company']
        );

        $this->belongsTo(
            'apps_id',
            'Gewaer\Models\Apps',
            'id',
            ['alias' => 'app']
        );

        $this->belongsTo(
            'apps_plans_id',
            'Gewaer\Models\AppsPlans',
            'id',
            ['alias' => 'appPlan']
        );
    }

    /**
     * Get the active subscription for this company app
     *
     * @return void
     */
    public static function getActiveForThisApp(): Subscription
    {
        $subscription = self::findFirst([
            'conditions' => 'company_id = ?0 and apps_id = ?1 and is_deleted  = 0',
            'bind' => [Di::getDefault()->getUserData()->default_company, Di::getDefault()->getApp()->getId()]
        ]);

        if (!is_object($subscription)) {
            throw new ServerErrorHttpException(_('No active subscription for this app ' . Di::getDefault()->getApp()->getId() . ' at the company ' . Di::getDefault()->getUserData()->default_company));
        }

        return $subscription;
    }
}
