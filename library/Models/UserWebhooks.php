<?php
declare(strict_types=1);

namespace Gewaer\Models;

class UserWebhooks extends AbstractModel
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
    public $webhooks_id;

    /**
     *
     * @var integer
     */
    public $apps_id;

    /**
     *
     * @var integer
     */
    public $users_id;

    /**
     *
     * @var integer
     */
    public $companies_id;

    /**
     *
     * @var string
     */
    public $url;

    /**
     *
     * @var string
     */
    public $method;

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
        $this->setSource('user_webhooks');

        $this->belongsTo(
            'webhooks_id',
            'Gewaer\Models\Webhooks',
            'id',
            ['alias' => 'webhooks']
        );

        $this->belongsTo(
            'users_id',
            'Gewaer\Models\Users',
            'id',
            ['alias' => 'users']
        );

        $this->belongsTo(
            'companies_id',
            'Gewaer\Models\Companies',
            'id',
            ['alias' => 'companies']
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
        return 'user_webhooks';
    }
}
