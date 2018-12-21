<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Gewaer\Exception\ModelException;

/**
 * Class Companies
 *
 * @package Gewaer\Models
 *
 * @property Users $user
 * @property Config $config
 * @property Apps $app
 * @property \Phalcon\Di $di
 */
class Companies extends \Gewaer\CustomFields\AbstractCustomFieldsModel
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
            'company_id',
            [
                'alias' => 'app',
                'params' => [
                    'conditions' => 'apps_id = ' . $this->di->getApp()->getId()
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
     * After creating the company
     *
     * @return void
     */
    public function afterCreate()
    {
        //setup the user notificatoin setting
        $companySettings = new CompaniesSettings();
        $companySettings->company_id = $this->getId();
        $companySettings->name = 'notifications';
        $companySettings->value = $this->user->email;
        if (!$companySettings->save()) {
            throw new Exception(current($companySettings->getMessages()));
        }

        //multi user asociation
        $usersAssociatedCompany = new UsersAssociatedCompany();
        $usersAssociatedCompany->users_id = $this->user->getId();
        $usersAssociatedCompany->company_id = $this->getId();
        $usersAssociatedCompany->identify_id = $this->user->getId();
        $usersAssociatedCompany->user_active = 1;
        $usersAssociatedCompany->user_role = 'admin';
        if (!$usersAssociatedCompany->save()) {
            throw new Exception(current($usersAssociatedCompany->getMessages()));
        }

        //now thta we setup de company and associated with the user we need to setup this as its default company
        if (!UserConfig::findFirst(['conditions' => 'users_id = ?0 and name = ?1', 'bind' => [$this->user->getId(), self::DEFAULT_COMPANY]])) {
            $userConfig = new UserConfig();
            $userConfig->users_id = $this->user->getId();
            $userConfig->name = self::DEFAULT_COMPANY;
            $userConfig->value = $this->getId();

            if (!$userConfig->save()) {
                throw new Exception(current($userConfig->getMessages()));
            }
        }

        /**
         * @var CompanyBranches
         */
        $branch = new CompanyBranches();
        $branch->companies_id = $this->getId();
        $branch->users_id = $this->user->getId();
        $branch->name = 'Default';
        $branch->is_default = 1;
        $branch->description = '';
        if (!$branch->save()) {
            throw new ModelException((string)current($branch->getMessages()));
        }

        //assign default branch to the user
        if (empty($this->user->default_company_branch)) {
            $this->user->default_company_branch = $branch->getId();
            $this->user->update();
        }

        //look for the default plan for this app
        $companyApps = new UserCompanyApps();
        $companyApps->company_id = $this->getId();
        $companyApps->apps_id = $this->di->getApp()->getId();

        //we need to assign this company to a plan
        if (empty($this->appPlanId)) {
            $plan = AppsPlans::getDefaultPlan();
            $companyApps->stripe_id = $plan->stripe_id;
        }

        $companyApps->subscriptions_id = 0;
        $companyApps->created_at = date('Y-m-d H:i:s');
        $companyApps->is_deleted = 0;

        if (!$companyApps->save()) {
            throw new ModelException((string)current($companyApps->getMessages()));
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
        if ($defaultCompany) {
            return self::findFirst($defaultCompany->value);
        }

        //second try
        $defaultCompany = UsersAssociatedCompany::findFirst([
            'conditions' => 'users_id = ?0 and user_active =?1',
            'bind' => [$user->getId(), 1],
        ]);

        if ($defaultCompany) {
            return self::findFirst($defaultCompany->company_id);
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
}
