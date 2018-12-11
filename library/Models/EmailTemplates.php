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

    /**
     * Retrieve email template by name
     * @param $name
     * @return EmailTemplates
     */
    public function getByName(string $name): EmailTemplates
    {
        $emailTemplate = self::findFirst([
            'conditions' => 'users_id = ?0 and company_id = ?1 and app_id = ?2 and name = ?3 and is_deleted = 0',
            'bind' => [$this->di->getUserData()->getId(), $this->di->getUserData()->defaulCompany->getId(), $this->di->getApp()->getId(), $name]
        ]);

        if (!$emailTemplate) {
            throw new UnprocessableEntityHttpException((string) current($emailTemplate->getMessages()));
        }

        return $emailTemplate;
    }
}
