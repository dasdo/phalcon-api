<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Gewaer\Exception\ServerErrorHttpException;
use Gewaer\Exception\ModelException;
use Phalcon\Di;

/**
 * Classs for UserCompanyAppsActivities
 * @property Users $userData
 * @property Request $request
 * @property Config $config
 * @property Apps $app
 * @property \Phalcon\DI $di
 *
 */
class UserCompanyAppsActivities extends AbstractModel
{
    /**
     *
     * @var integer
     */
    public $companies_id;

    /**
     *
     * @var integer
     */
    public $company_branches_id;

    /**
     *
     * @var integer
     */
    public $apps_id;

    /**
     *
     * @var string
     */
    public $key;

    /**
     *
     * @var string
     */
    public $value;

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
            'company_branches_id',
            'Gewaer\Models\CompanyBranches',
            'id',
            ['alias' => 'companyBranch']
        );

        $this->setSource('user_company_apps_activities');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource() : string
    {
        return 'user_company_apps_activities';
    }

    /**
     * Get the value of the settins by it key
     *
     * @param string $key
     * @param string $value
     */
    public static function get(string $key) : string
    {
        $setting = self::findFirst([
            'conditions' => 'companies_id = ?0 and apps_id = ?1 and key = ?2',
            'bind' => [Di::getDefault()->getUserData()->default_company, Di::getDefault()->getApp()->getId(), $key]
        ]);

        if (is_object($setting)) {
            return $setting->value;
        }

        throw new ServerErrorHttpException(_('No settings found with this ' . $key));
    }

    /**
     * Set a setting for the given app
     *
     * @param string $key
     * @param string $value
     */
    public static function set(string $key, $value) : bool
    {
        $activity = self::findFirst([
            'conditions' => 'companies_id = ?0 and apps_id = ?1 and key = ?2',
            'bind' => [Di::getDefault()->getUserData()->default_company, Di::getDefault()->getApp()->getId(), $key]
        ]);

        if (!is_object($activity)) {
            $activity = new self();
            $activity->companies_id = Di::getDefault()->getUserData()->default_company;
            $activity->company_branches_id = Di::getDefault()->getUserData()->default_company_branch;
            $activity->apps_id = Di::getDefault()->getApp()->getId();
            $activity->key = $key;
        }

        $activity->value = $value;

        if (!$activity->save()) {
            throw new ModelException((string)current($activity->getMessages()));
        }

        return true;
    }
}
