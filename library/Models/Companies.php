<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Gewaer\Exception\ServerErrorHttpException;

/**
 * Class Companies
 *
 * @package Gewaer\Models
 *
 * @property Users $user
 * @property CompanyBranches $branch
 * @property CompanyBranches $branches
 * @property Config $config
 * @property UserCompanyApps $app
 * @property \Phalcon\Di $di
 */
class Companies extends \Baka\Auth\Models\Companies
{
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $profile_image;

    /**
     *
     * @var string
     */
    public $website;

    /**
     *
     * @var integer
     */
    public $users_id;

    /**
     *
     * @var integer
     */
    public $has_activities;

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
     * Provide the app plan id
     *
     * @var integer
     */
    public $appPlanId = null;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('companies');

        $this->belongsTo(
            'users_id',
            'Gewaer\Models\Users',
            'id',
            ['alias' => 'user']
        );

        $this->hasMany(
            'id',
            'Gewaer\Models\CompanyBranches',
            'company_id',
            ['alias' => 'branches']
        );

        $this->hasMany(
            'id',
            'Gewaer\Models\UsersAssociatedCompany',
            'company_id',
            ['alias' => 'UsersAssociatedCompany']
        );

        $this->hasOne(
            'id',
            'Gewaer\Models\CompanyBranches',
            'company_id',
            [
                'alias' => 'branch',
            ]
        );

        $this->hasOne(
            'id',
            'Gewaer\Models\UserCompanyApps',
            'company_id',
            [
                'alias' => 'app',
                'params' => [
                    'conditions' => 'apps_id = ' . $this->di->getApp()->getId()
                ]
            ]
        );

        $this->hasOne(
            'id',
            'Gewaer\Models\UserCompanyApps',
            'company_id',
            [
                'alias' => 'apps',
                'params' => [
                    'conditions' => 'apps_id = ' . $this->di->getApp()->getId()
                ]
            ]
        );

        $this->hasOne(
            'id',
            'Gewaer\Models\Subscription',
            'company_id',
            [
                'alias' => 'subscription',
                'params' => [
                    'conditions' => 'apps_id = ' . $this->di->getApp()->getId() . ' AND ends_at is null AND is_deleted = 0 ',
                    'order' => 'id DESC'
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Gewaer\Models\Subscription',
            'company_id',
            [
                'alias' => 'subscriptions',
                'params' => [
                    'conditions' => 'apps_id = ' . $this->di->getApp()->getId() . ' AND is_deleted = 0',
                    'order' => 'id DESC'
                ]
            ]
        );
    }

    /**
     * Model validation
     *
     * @return void
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'name',
            new PresenceOf([
                'model' => $this,
                'required' => true,
            ])
        );

        return $this->validate($validator);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource() : string
    {
        return 'companies';
    }

    /**
     * Confirm if a user belongs to this current company
     *
     * @param Users $user
     * @return boolean
     */
    public function userAssociatedToCompany(Users $user): bool
    {
        return is_object($this->getUsersAssociatedCompany('users_id =' . $user->getId())) ? true : false;
    }

    /**
     * After creating the company
     *
     * @return void
     */
    public function afterCreate()
    {
        parent::afterCreate();

        /**
         * @var CompanyBranches
         */
        $branch = new CompanyBranches();
        $branch->company_id = $this->getId();
        $branch->users_id = $this->user->getId();
        $branch->name = 'Default';
        $branch->is_default = 1;
        $branch->description = '';
        if (!$branch->save()) {
            throw new ServerErrorHttpException((string)current($branch->getMessages()));
        }

        //look for the default plan for this app
        $companyApps = new UserCompanyApps();
        $companyApps->company_id = $this->getId();
        $companyApps->apps_id = $this->di->getApp()->getId();
        $companyApps->subscriptions_id = 0;

        //we need to assign this company to a plan
        if (empty($this->appPlanId)) {
            $plan = AppsPlans::getDefaultPlan();
            $companyApps->stripe_id = $plan->stripe_id;
        }

        $companyApps->created_at = date('Y-m-d H:i:s');
        $companyApps->is_deleted = 0;

        if (!$companyApps->save()) {
            throw new ServerErrorHttpException((string)current($companyApps->getMessages()));
        }
    }
}
