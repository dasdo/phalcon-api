<?php
declare(strict_types=1);

namespace Gewaer\Models;

class FileSystem extends AbstractModel
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
    public $companies_id;

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
    public $system_modules_id;

    /**
     *
     * @var integer
     */
    public $entity_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $path;

    /**
     *
     * @var string
     */
    public $url;

    /**
     *
     * @var string
     */
    public $size;

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
     * @var int
     */
    public $is_deleted;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('filesystem');

        $this->belongsTo(
            'apps_id',
            'Gewaer\Models\Apps',
            'id',
            ['alias' => 'app']
        );

        $this->belongsTo(
            'users_id',
            'Gewaer\Models\Users',
            'id',
            ['alias' => 'user']
        );

        $this->belongsTo(
            'companies_id',
            'Gewaer\Models\Companies',
            'id',
            ['alias' => 'company']
        );

        $this->belongsTo(
            'system_modules_id',
            'Gewaer\Models\SystemModules',
            'id',
            ['alias' => 'systemModules']
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource() : string
    {
        return 'filesystem';
    }
}
