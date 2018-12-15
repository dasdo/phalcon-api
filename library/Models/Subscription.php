<?php

namespace Gewaer\Models;

use Phalcon\Cashier\Subscription as PhalconSubscription;
use Gewaer\Exception\ServerErrorHttpException;

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
    public function getActiveForThisApp(): Subscription
    {
        $subscription = self::findFirst([
            'conditions' => 'company_id = ?1 and apps_id = ?2 and is_deleted  = 0',
            'bind' => [$this->di->getUserData()->default_company, $this->di->getApp()->getId()]
        ]);

        if (!is_object($subscription)) {
            throw new ServerErrorHttpException(_('No active subscription for this app ' . $this->di->getApp()->getId()));
        }

        return $subscription;
    }
}
