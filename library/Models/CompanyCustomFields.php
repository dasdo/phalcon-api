<?php
declare(strict_types=1);

namespace Gewaer\Models;

class CompanyCustomFields extends AbstractModel
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
    public $company_id;

    /**
     *
     * @var integer
     */
    public $custom_field_id;

    /**
     *
     * @var string
     */
    public $value;

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
        $this->setSource('company_custom_fields');

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
        return 'company_custom_fields';
    }
}
