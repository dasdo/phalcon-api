<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Gewaer\Exception\ServerErrorHttpException;
use Exception;
use Carbon\Carbon;
use Gewaer\Traits\ModelSettingsTrait;

/**
 * Class Companies
 *
 * @package Gewaer\Models
 *
 * @property Users $user
 * @property Users $userData
 * @property DefaultCompany $default_company
 * @property CompaniesBranches $branch
 * @property CompaniesBranches $branches
 * @property Config $config
 * @property UserCompanyApps $app
 * @property \Phalcon\Di $di
 */
class Companies extends \Gewaer\CustomFields\AbstractCustomFieldsModel
{
    use ModelSettingsTrait;

    const DEFAULT_COMPANY = 'DefaulCompany';
    const PAYMENT_GATEWAY_CUSTOMER_KEY = 'payment_gateway_customer_id';

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
     *
     * @var integer
     */
    public $currency_id;

    /**
     *
     * @var string
     */
    public $language;

    /**
     *
     * @var string
     */
    public $timezone;

    /**
     *
     * @var string
     */
    public $currency;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('companies');

        $this->belongsTo('users_id', 'Baka\Auth\Models\Users', 'id', ['alias' => 'user']);
        $this->hasMany('id', 'Baka\Auth\Models\CompanySettings', 'id', ['alias' => 'settings']);

        $this->belongsTo(
            'users_id',
            'Gewaer\Models\Users',
            'id',
            ['alias' => 'user']
        );

        $this->hasMany(
            'id',
            'Gewaer\Models\CompaniesBranches',
            'companies_id',
            ['alias' => 'branches']
        );

        $this->hasMany(
            'id',
            'Gewaer\Models\CompaniesCustomFields',
            'companies_id',
            ['alias' => 'fields']
        );

        $this->hasMany(
            'id',
            'Gewaer\CustomFields\CustomFields',
            'companies_id',
            ['alias' => 'custom-fields']
        );

        $this->hasMany(
            'id',
            'Gewaer\Models\UsersAssociatedCompany',
            'companies_id',
            ['alias' => 'UsersAssociatedCompany']
        );

        $this->hasOne(
            'id',
            'Gewaer\Models\CompaniesBranches',
            'companies_id',
            [
                'alias' => 'branch',
            ]
        );

        $this->hasOne(
            'id',
            'Gewaer\Models\UserCompanyApps',
            'companies_id',
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
            'companies_id',
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
            'companies_id',
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
            'companies_id',
            [
                'alias' => 'subscriptions',
                'params' => [
                    'conditions' => 'apps_id = ' . $this->di->getApp()->getId() . ' AND is_deleted = 0',
                    'order' => 'id DESC'
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Gewaer\Models\UserWebhooks',
            'companies_id',
            ['alias' => 'user-webhooks']
        );

        $systemModule = SystemModules::getSystemModuleByModelName(self::class);
        $this->hasMany(
            'id',
            'Gewaer\Models\FileSystem',
            'entity_id',
            [
                'alias' => 'filesystem',
                'conditions' => 'system_modules_id = ?0',
                'bind' => [$systemModule->getId()]
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
    * Register a company given a user and name
    *
    * @param  Users  $user
    * @param  string $name
    * @return Companies
    */
    public static function register(Users $user, string $name): Companies
    {
        $company = new self();
        $company->name = $name;
        $company->users_id = $user->getId();

        if (!$company->save()) {
            throw new Exception(current($company->getMessages()));
        }

        return $company;
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
     * Get the stripe customer id from the
     *
     * @return ?string
     */
    public function getPaymentGatewayCustomerId(): ?string
    {
        return $this->getSettings(self::PAYMENT_GATEWAY_CUSTOMER_KEY);
    }

    /**
     * Before crate company
     *
     * @return void
     */
    public function beforeCreate()
    {
        parent::beforeCreate();

       
        $this->language = AppsSettings::getDefaultAppsSettingsByName('language');
        $this->timezone = AppsSettings::getDefaultAppsSettingsByName('timezone');
        $this->currency = Currencies::findFirstByCode(AppsSettings::getDefaultAppsSettingsByName('currency'))->getId();
    }

    /**
     * After creating the company
     *
     * @return void
     */
    public function afterCreate()
    {
        parent::afterCreate();

        //setup the user notificatoin setting
        $this->setSettings('notifications', $this->user->email);

        //multi user asociation
        $usersAssociatedCompany = new UsersAssociatedCompany();
        $usersAssociatedCompany->users_id = $this->user->getId();
        $usersAssociatedCompany->companies_id = $this->getId();
        $usersAssociatedCompany->identify_id = $this->user->getId();
        $usersAssociatedCompany->user_active = 1;
        $usersAssociatedCompany->user_role = 'admin';
        if (!$usersAssociatedCompany->save()) {
            throw new Exception((string)current($usersAssociatedCompany->getMessages()));
        }

        //now thta we setup de company and associated with the user we need to setup this as its default company
        if (!UserConfig::findFirst(['conditions' => 'users_id = ?0 and name = ?1', 'bind' => [$this->user->getId(), self::DEFAULT_COMPANY]])) {
            $userConfig = new UserConfig();
            $userConfig->users_id = $this->user->getId();
            $userConfig->name = self::DEFAULT_COMPANY;
            $userConfig->value = $this->getId();

            if (!$userConfig->save()) {
                throw new Exception((string)current($userConfig->getMessages()));
            }
        }

        /**
         * @var CompaniesBranches
         */
        $branch = new CompaniesBranches();
        $branch->companies_id = $this->getId();
        $branch->users_id = $this->user->getId();
        $branch->name = 'Default';
        $branch->is_default = 1;
        $branch->description = '';
        if (!$branch->save()) {
            throw new ServerErrorHttpException((string)current($branch->getMessages()));
        }

        //look for the default plan for this app
        $companyApps = new UserCompanyApps();
        $companyApps->companies_id = $this->getId();
        $companyApps->apps_id = $this->di->getApp()->getId();
        //$companyApps->subscriptions_id = 0;

        //we need to assign this company to a plan
        if (empty($this->appPlanId)) {
            $plan = AppsPlans::getDefaultPlan();
            $companyApps->stripe_id = $plan->stripe_id;
        }

        //If the newly created company is not the default then we create a new subscription with the same user
        if ($this->di->getUserData()->default_company != $this->getId()) {
            $this->setSettings(self::PAYMENT_GATEWAY_CUSTOMER_KEY, $this->startFreeTrial());
        }

        $companyApps->subscriptions_id = $this->subscription->getId();
        $companyApps->created_at = date('Y-m-d H:i:s');
        $companyApps->is_deleted = 0;

        if (!$companyApps->save()) {
            throw new ServerErrorHttpException((string)current($companyApps->getMessages()));
        }
    }

    /**
     * Get the default company the users has selected
     *
     * @param  Users  $user
     * @return Companies
     */
    public static function getDefaultByUser(Users $user): Companies
    {
        //verify the user has a default company
        $defaultCompany = UserConfig::findFirst([
            'conditions' => 'users_id = ?0 and name = ?1',
            'bind' => [$user->getId(), self::DEFAULT_COMPANY],
        ]);

        //found it
        if (is_object($defaultCompany)) {
            return self::findFirst($defaultCompany->value);
        }

        //second try
        $defaultCompany = UsersAssociatedCompany::findFirst([
            'conditions' => 'users_id = ?0 and user_active =?1',
            'bind' => [$user->getId(), 1],
        ]);

        if (is_object($defaultCompany)) {
            return self::findFirst($defaultCompany->companies_id);
        }

        throw new Exception(_("User doesn't have an active company"));
    }

    /**
     * After the model was update we need to update its custom fields
     *
     * @return void
     */
    public function afterUpdate()
    {
        //only clean and change custom fields if they are been sent
        if (!empty($this->customFields)) {
            //replace old custom with new
            $allCustomFields = $this->getAllCustomFields();
            if (is_array($allCustomFields)) {
                foreach ($this->customFields as $key => $value) {
                    $allCustomFields[$key] = $value;
                }
            }

            if (!empty($allCustomFields)) {
                //set
                $this->setCustomFields($allCustomFields);
                //clean old
                $this->cleanCustomFields($this->getId());
                //save new
                $this->saveCustomFields();
            }
        }
    }

    /**
     * Start a free trial for a new company
     *
     * @return string //the customer id
     */
    public function startFreeTrial() : ?string
    {
        $defaultPlan = AppsPlans::getDefaultPlan();
        $trialEndsAt = Carbon::now()->addDays($this->di->getApp()->plan->free_trial_dates);

        //Lets create a new default subscription without payment method
        $this->user->newSubscription($defaultPlan->name, $defaultPlan->stripe_id, $this, $this->di->getApp())
                ->trialDays($defaultPlan->free_trial_dates)
                ->create();

        //ook for the subscription and update the missing info
        $subscription = $this->subscription;
        $subscription->apps_plans_id = $this->di->getApp()->default_apps_plan_id;
        $subscription->trial_ends_days = $trialEndsAt->diffInDays(Carbon::now());
        $subscription->is_freetrial = 1;
        $subscription->is_active = 1;

        if (!$subscription->save()) {
            throw new ServerErrorHttpException((string)'Subscription for new company couldnt be created ' . current($this->getMessages()));
        }

        return $this->user->stripe_id;
    }
}
