<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Phalcon\Di;

class SystemModules extends AbstractModel
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
    public $slug;

    /**
     *
     * @var string
     */
    public $model_name;

    /**
     *
     * @var integer
     */
    public $apps_id;

    /**
     *
     * @var integer
     */
    public $parents_id;

    /**
     *
     * @var integer
     */
    public $menu_order;

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
        $this->hasMany(
            'id',
            'Gewaer\Models\EmailTemplatesVariables',
            'system_modules_id',
            ['alias' => 'templateVariable']
        );

        $this->hasMany(
            'id',
            'Gewaer\Models\Webhooks',
            'system_modules_id',
            ['alias' => 'webhook']
        );

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
    public function getSource(): string
    {
        return 'system_modules';
    }

    /**
     * Get System Module by its model_name
     * @param string $model_name
     * @return SystemModules
     */
    public static function getSystemModuleByModelName(string $modelName):SystemModules
    {
        return SystemModules::findFirst([
            'conditions' => 'model_name = ?0 and apps_id = ?1',
            'bind' => [$modelName, Di::getDefault()->getApp()->getId()]
        ]);
    }
}
