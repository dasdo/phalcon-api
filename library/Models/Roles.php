<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Gewaer\Exception\ServerErrorHttpException;
use Gewaer\Models\Companies;
use Baka\Auth\Models\Companies as BakaCompanies;
use Phalcon\Di;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;




class Roles extends AbstractModel
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
    public $description;

    /**
     *
     * @var integer
     */
    public $scope;

    /**
     *
     * @var integer
     */
    public $company_id;

    /**
     *
     * @var int
     */
    public $apps_id;

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
     * Default ACL company
     *
     */
    const DEFAULT_ACL_COMPANY_ID = 0;
    const DEFAULT_ACL_APP_ID = 0;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('roles');

        $this->hasMany(
            'id',
            'Gewaer\Models\AccessList',
            'roles_id',
            ['alias' => 'accesList']
        );
    }

    /**
     * Validations and business logic
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'name',
            new PresenceOf([
                'field' => 'name',
                'required' => true,
            ])
        );

        $validator->add(
            'description',
            new PresenceOf([
                'field' => 'description',
                'required' => true,
            ])
        );

        $validator->add(
            'name',
            new StringLength([
                'max' => 32,
                'messageMinimum' => _('Role Name. Maxium 32 characters.'),
            ])
        );

        return $this->validate($validator);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'roles';
    }

    /**
     * Get the entity by its name
     *
     * @param string $name
     * @return void
     */
    public static function getByName(string $name)
    {
        return self::findFirst([
            'conditions' => 'name = ?0 AND company_id = ?1 AND apps_id = ?2 AND is_deleted = 0',
            'bind' => [$name, Di::getDefault()->getUserData()->default_company, Di::getDefault()->getApp()->getId()]
        ]);
    }

    /**
     * Get the entity by its name
     *
     * @param string $name
     * @return void
     */
    public static function getById(int $id)
    {
        return self::findFirst([
            'conditions' => 'id = ?0 AND company_id in (?1, ?2) AND apps_id in (?3, ?4) AND is_deleted = 0',
            'bind' => [$id, Di::getDefault()->getUserData()->default_company, Apps::GEWAER_DEFAULT_APP_ID, Di::getDefault()->getApp()->getId(), Apps::GEWAER_DEFAULT_APP_ID]
        ]);
    }

    /**
     * Get the Role by it app name
     *
     * @param string $role
     * @return Roles
     */
    public static function getByAppName(string $role, Companies $company): Roles
    {
        //echeck if we have a dot , taht means we are sending the specific app to use
        if (strpos($role, '.') === false) {
            throw new ServerErrorHttpException('ACL - We are expecting the app for this role');
        }

        $appRole = explode('.', $role);
        $role = $appRole[1];
        $appName = $appRole[0];

        //look for the app and set it
        if (!$app = Apps::getACLApp($appName)) {
            throw new ServerErrorHttpException('ACL - No app found for this role');
        }

        return self::findFirst([
            'conditions' => 'apps_id in (?0, ?1) AND company_id in (?2 , ?3)',
            'bind' => [$app->getId(), self::DEFAULT_ACL_APP_ID, $company->getId(), self::DEFAULT_ACL_COMPANY_ID]
        ]);
    }

    /**
     * Duplicate a role with it access list
     *
     * @return bool
     */
    public function copy(): Roles
    {
        $accesList = $this->accesList;

        //remove id to create new record
        $this->name .= 'Copie';
        $this->scope = 1;
        $this->id = null;
        $this->company_id = $this->di->getUserData()->default_company;
        $this->apps_id = $this->di->getApp()->getId();
        $this->save();

        foreach ($accesList as $access) {
            $copyAccessList = new AccessList();
            $copyAccessList->apps_id = $this->apps_id;
            $copyAccessList->roles_id = $this->getId();
            $copyAccessList->roles_name = $this->name;
            $copyAccessList->resources_name = $access->resources_name;
            $copyAccessList->access_name = $access->access_name;
            $copyAccessList->allowed = $access->allowed;
            $copyAccessList->create();
        }

        return $this;
    }
}
