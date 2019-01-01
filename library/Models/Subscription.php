<?php

namespace Gewaer\Models;

use Phalcon\Cashier\Subscription as PhalconSubscription;
use Gewaer\Exception\ServerErrorHttpException;
use Phalcon\Di;

/**
 * Trait Subscription
 *
 * @package Gewaer\Models
 *
 * @property Users $user
 * @property AppsPlans $appPlan
 * @property CompanyBranches $branches
 * @property Companies $company
 * @property UserCompanyApps $app
 * @property \Phalcon\Di $di
 *
 */
class Subscription extends PhalconSubscription
{
    /**
     *
     * @var integer
     */
    public $apps_plans_id = 0;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $companies_id;

    /**
     *
     * @var integer
     */
    public $apps_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $stripe_id;

    /**
     *
     * @var string
     */
    public $stripe_plan;

    /**
     *
     * @var integer
     */
    public $quantity;

    /**
     *
     * @var string
     */
    public $trial_ends_at;

    /**
     *
     * @var string
     */
    public $created_at;

    /**
     *
     * @var string
     */
    public $updated_at;

    /**
     *
     * @var integer
     */
    public $is_deleted;

    /**
     * Initialize
     *
     * @return void
     */
    public function initialize()
    {
        $this->belongsTo('user_id', 'Gewaer\Models\Users', 'id', ['alias' => 'user']);

        $this->belongsTo(
            'companies_id',
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
    public static function getActiveForThisApp() : Subscription
    {
        $subscription = self::findFirst([
            'conditions' => 'companies_id = ?0 and apps_id = ?1 and is_deleted  = 0',
            'bind' => [Di::getDefault()->getUserData()->currentCompanyId(), Di::getDefault()->getApp()->getId()]
        ]);

        if (!is_object($subscription)) {
            throw new ServerErrorHttpException(_('No active subscription for this app ' . Di::getDefault()->getApp()->getId() . ' at the company ' . Di::getDefault()->getUserData()->currentCompanyId()));
        }

        return $subscription;
    }
}
