<?php
declare(strict_types=1);

namespace Gewaer\Models;

class EmailTemplates extends AbstractModel
{
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $company_id;

    /**
     *
     * @var integer
     */
    public $app_id;

    /**
     *
     * @var integer
     */
    public $name;

    /**
     *
     * @var integer
     */
    public $template;

    /**
     *
     * @var string
     */
    public $users_id;

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
        $this->setSource('email_templates');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'email_templates';
    }
}
