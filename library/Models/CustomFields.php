<?php
declare(strict_types=1);

namespace Gewaer\Models;

class CustomFields extends AbstractModel
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
    public $users_id;

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
    public $name;

    /**
     *
     * @var integer
     */
    public $modules_id;

    /**
     *
     * @var integer
     */
    public $fields_type_id;

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
        $this->setSource('custom_fields');

        // $this->belongsTo(
        //     'company_id',
        //     'Gewaer\Models\Companies',
        //     'id',
        //     ['alias' => 'company']
        // );

        // $this->belongsTo(
        //     'apps_id',
        //     'Gewaer\Models\Apps',
        //     'id',
        //     ['alias' => 'app']
        // );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'custom_fields';
    }
}
