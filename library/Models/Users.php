<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Gewaer\Traits\PermissionsTrait;
use Phalcon\Cashier\Billable;
use Gewaer\Exception\UnprocessableEntityHttpException;

/**
 * Class Users
 *
 * @package Gewaer\Models
 *
 * @property Users $user
 * @property Config $config
 * @property Apps $app
 * @property \Phalcon\Di $di
 */
class Users extends \Baka\Auth\Models\Users
{
    use PermissionsTrait;
    use Billable;

    public $default_company_branch;
    public $roles_id;
    public $stripe_id;
    public $card_last_four;
    public $card_brand;
    public $trial_ends_at;

    /**
     * Provide the app plan id
     * if the user is signing up a new company
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

        $this->setSource('users');

        $this->hasOne(
            'id',
            'Gewaer\Models\UserRoles',
            'users_id',
            ['alias' => 'permission']
        );

        $this->hasMany(
            'id',
            'Gewaer\Models\UserRoles',
            'users_id',
            ['alias' => 'permissions']
        );

        $this->hasManyToMany(
            'id',
            'Gewaer\Models\UserRoles',
            'users_id',
            'roles_id',
            'Gewaer\Models\Roles',
            'id',
            [
                'alias' => 'roles',
                'params' => [
                    'limit' => 1,
                    'conditions' => 'Gewaer\Models\UserRoles.apps_id = ' . $this->di->getConfig()->app->id,
                ]
            ]
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'users';
    }

    /**
     * Get the User key for redis
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->id;
    }

    /**
     * Get all of the subscriptions for the user.
     */
    public function subscriptions()
    {
        $this->hasMany(
            'id',
            Subscription::class,
            'user_id',
            [
                'alias' => 'subscriptions',
                'params' => [
                    'conditions' => 'apps_id = ?0 and company_id = ?1',
                    'bind' => [$this->di->getApp()->getId(), $this->default_company],
                    'order' => 'id DESC'
                ]
            ]
        );
        return $this->getRelated('subscriptions');
    }

    /**
     * Before create
     *
     * @return void
     */
    public function beforeCreate()
    {
        parent::beforeCreate();

        //Assign admin role to the system if we dont get a specify role
        if (empty($this->roles_id)) {
            $role = Roles::findFirstByName('Admins');
            $this->roles_id = $role->getId();
        }
    }

    /**
     * What to do after the creation of a new users
     *  - Assign default role
     *
     * @return void
     */
    public function afterCreate()
    {
        if (empty($this->default_company)) {
            parent::afterCreate();
        }

        //Create new company associated company
        $newUserAssocCompany = new UsersAssociatedCompany();
        $newUserAssocCompany->users_id = $this->id;
        $newUserAssocCompany->company_id = $this->default_company;
        $newUserAssocCompany->identify_id = 1;
        $newUserAssocCompany->user_active = 1;
        $newUserAssocCompany->user_role = $this->roles_id == 1 ? 'admins' : 'users';

        if (!$newUserAssocCompany->save()) {
            throw new UnprocessableEntityHttpException((string) current($newUserAssocCompany->getMessages()));
        }

        //Insert record into user_roles
        $userRole = new UserRoles();
        $userRole->users_id = $this->id;
        $userRole->roles_id = $this->roles_id;
        $userRole->apps_id = $this->di->getApp()->getId();
        $userRole->company_id = $this->default_company;

        if (!$userRole->save()) {
            throw new UnprocessableEntityHttpException((string) current($userRole->getMessages()));
        }
    }
}
