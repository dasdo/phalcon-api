<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Gewaer\Exception\ServerErrorHttpException;
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
     * @var integer
     */
    public $name;

    /**
     *
     * @var integer
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
            'roles_name',
            'Gewaer\Models\Roles',
            'name',
            ['alias' => 'role']
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
            'conditions' => 'name = ?0 AND company_id = ?1 AND apps_id = ?2 AND is_delete = 0',
            'bind' => [$name, Di::getDefault()->getUserData()->default_company, Di::getDefault()->getApp()->getId()]
        ]);
    }

    /**
     * Get the Role by it app name
     *
     * @param string $role
     * @return Roles
     */
    public static function getByAppName(string $role, BakaCompanies $company): Roles
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
}
