<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Gewaer\Exception\ServerErrorHttpException;
use Gewaer\Exception\ModelException;

class UserCompanyAppsActivities extends AbstractModel
{
    /**
     *
     * @var integer
     */
    public $company_id;

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
     * @var integer
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
        parent::initialize();

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
    public function getSource(): string
    {
        return 'user_company_apps_activities';
    }

    /**
    * Get the value of the settins by it key
    *
    * @param string $key
    * @param string $value
    */
    public function get(string $key) : string
    {
        $setting = self::findFirst([
            'conditions' => 'company_id = ?0 and apps_id = ?1 and key = ?2',
            'bind' => [$this->di->getUserData()->default_company, $this->di->getApp()->getId(), $key]
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
    public function set(string $key, string $value) : bool
    {
        $activity = new self();
        $activity->company_id = $this->di->getUserData()->default_company;
        $activity->default_company_branch = $this->di->getUserData()->default_company_bran;
        $activity->apps_id = $this->di->getApp()->getId();
        $activity->key = $key;
        $activity->value = $value;

        if (!$activity->save()) {
            throw new ModelException((string) current($activity->getMessages()));
        }

        return true;
    }
}
