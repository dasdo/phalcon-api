<?php

declare(strict_types=1);

namespace Gewaer\Models;

use Phalcon\Di;
use Gewaer\Exception\ModelException;

class AccessList extends AbstractModel
{
    /**
     *
     * @var string
     */
    public $roles_name;

    /**
     *
     * @var string
     */
    public $resources_name;

    /**
     *
     * @var string
     */
    public $access_name;

    /**
     *
     * @var integer
     */
    public $allowed;

    /**
     *
     * @var integer
     */
    public $apps_id;

    /**
     *
     * @var integer
     */
    public $roles_id;

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
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('access_list');

        $this->belongsTo(
            'roles_name',
            'Gewaer\Models\Roles',
            'name',
            ['alias' => 'role']
        );
    }

    /**
     * Delete all the accest list records for this given role
     *
     * @param Roles $role
     * @return void
     */
    public static function deleteAllByRole(Roles $role): bool
    {
        return (bool) self::find('roles_id = ' . $role->getId())->delete();
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'access_list';
    }

    /**
     * Given the resource and access check if exist
     *
     * @param Roles $role
     * @param string $resourceName
     * @param string $accessName
     * @return integer
     */
    public static function exist(Roles $role, string $resourceName, string $accessName): int
    {
        return self::count([
            'conditions' => 'roles_id = ?0 and resources_name = ?1 AND access_name = ?2 AND apps_id = ?3',
            'bind' => [$role->getId(), $resourceName, $accessName, Di::getDefault()->getAcl()->getApp()->getId()]
        ]);
    }

    /**
     * Given the resource and access check if exist
     *
     * @param Roles $role
     * @param string $resourceName
     * @param string $accessName
     * @return integer
     */
    public static function getBy(Roles $role, string $resourceName, string $accessName) : AccessList
    {
        $access = self::findFirst([
            'conditions' => 'roles_id = ?0 and resources_name = ?1 AND access_name = ?2 AND apps_id = ?3',
            'bind' => [$role->getId(), $resourceName, $accessName, Di::getDefault()->getAcl()->getApp()->getId()]
        ]);

        if (!is_object($access)) {
            throw new ModelException(_('Access for role ' . $role->name . ' with resource ' . $resourceName . '-' . $accessName . ' not found on this app ' . Di::getDefault()->getAcl()->getApp()->getId() . ' AND Company' . Di::getDefault()->getAcl()->getCompany()->getId()));
        }

        return $access;
    }
}
