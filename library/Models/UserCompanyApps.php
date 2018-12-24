<?php
declare(strict_types=1);

namespace Gewaer\Models;

class UserCompanyApps extends \Baka\Auth\Models\UserCompanyApps
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
    public $apps_id;

    /**
     *
     * @var string
     */
    public $stripe_id;

    /**
     *
     * @var integer
     */
    public $subscriptions_id;

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

        $this->setSource('user_company_apps');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource() : string
    {
        return 'user_company_apps';
    }

    /**
     * Get the current company app
     *
     * @return void
     */
    public static function getCurrentApp()
    {
        return self::findFirst([
            'conditions' => 'company_id = ?0 and apps_id = ?1',
            'bind' => [Di::getDefault()->getUserData()->defaultCompany->getId(), Di::getDefault()->getApp()->getId()]
        ]);
    }
}
