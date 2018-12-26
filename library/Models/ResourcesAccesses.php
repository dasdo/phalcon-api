<?php

declare(strict_types=1);

namespace Gewaer\Models;

class ResourcesAccesses extends AbstractModel
{
    /**
     *
     * @var integer
     */
    public $resources_id;

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
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('resources_accesses');

        $this->belongsTo(
            'resources_id',
            'Gewaer\Models\Resources',
            'id',
            ['alias' => 'resources']
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'resources_accesses';
    }
}
