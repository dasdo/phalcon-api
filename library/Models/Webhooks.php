<?php
declare(strict_types=1);

namespace Gewaer\Models;

class Webhooks extends AbstractModel
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
    public $system_modules_id;

    /**
     *
     * @var integer
     */
    public $apps_id;

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
     * @var string
     */
    public $action;

    /**
     *
     * @var string
     */
    public $format;

    /**
     *
     * @var integer
     */
    public $is_deleted;

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
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('webhooks');

        $this->hasMany(
            'id',
            'Gewaer\Models\UserWebhooks',
            'webhooks_id',
            ['alias' => 'user-webhooks']
        );

        $this->belongsTo(
            'system_modules_id',
            'Gewaer\Models\SystemModules',
            'id',
            ['alias' => 'modules']
        );

        $this->belongsTo(
            'apps_id',
            'Gewaer\Models\Apps',
            'id',
            ['alias' => 'apps']
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'webhooks';
    }
}
