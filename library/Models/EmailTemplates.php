<?php
declare(strict_types=1);

namespace Gewaer\Models;

use Gewaer\Exception\UnprocessableEntityHttpException;
use \Phalcon\Di;

/**
 * Classs for Email Templates
 * @property Users $userData
 * @property Request $request
 * @property Config $config
 * @property Apps $app
 * @property \Phalcon\DI $di
 *
 */
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
            'users_id',
            'Gewaer\Models\Users',
            'id',
            ['alias' => 'user']
        );

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

    /**
     * Retrieve email template by name
     * @param $name
     * @return EmailTemplates
     */
    public function getByName(string $name): EmailTemplates
    {
        $emailTemplate = self::findFirst([
            'conditions' => 'company_id = ?1 and app_id = ?2 and name = ?3 and is_deleted = 0',
            'bind' => [Di::getDefault()->getUserData()->default_company, Di::getDefault()->getConfig()->app->id, $name]
        ]);

        if (!is_object($emailTemplate)) {
            throw new UnprocessableEntityHttpException((string) current($emailTemplate->getMessages()));
        }

        return $emailTemplate;
    }
}
