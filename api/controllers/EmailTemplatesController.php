<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\EmailTemplates;
use Gewaer\Exception\NotFoundHttpException;
use Gewaer\Exception\UnprocessableEntityHttpException;
use Phalcon\Security\Random;
use Phalcon\Http\Response;

/**
 * Class LanguagesController
 *
 * @package Gewaer\Api\Controllers
 *
 * @property Users $userData
 * @property Request $request
 * @property Config $config
 * @property \Baka\Mail\Message $mail
 * @property Apps $app
 *
 */
class EmailTemplatesController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['users_id', 'companies_id', 'apps_id', 'name', 'template'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['users_id', 'companies_id', 'apps_id', 'name', 'template'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new EmailTemplates();
        $this->additionalSearchFields = [
            ['is_deleted', ':', '0'],
            ['companies_id', ':', '0|' . $this->userData->currentCompanyId()],
        ];
    }

    /**
     * Add a new by copying a specific email template based on
     *
     * @method POST
     * @url /v1/data
     *
     * @return \Phalcon\Http\Response
     */
    public function create(): Response
    {
        $request = $this->request->getPost();

        if (empty($request)) {
            $request = $this->request->getJsonRawBody(true);
        }

        //Find email template based on the basic parameters
        $existingEmailTemplate = $this->model::findFirst([
            'conditions' => 'users_id = ?0 and companies_id = ?1 and apps_id = ?2 and name = ?3 and is_deleted = 0',
            'bind' => [$this->userData->getId(), $this->userData->default_company, $this->app->getId(), $request['name']]
        ]);

        if (!is_object($existingEmailTemplate)) {
            throw new NotFoundHttpException('Email Template not found');
        }

        $random = new Random();
        $randomInstance = $random->base58();

        $request['users_id'] = $existingEmailTemplate->users_id;
        $request['companies_id'] = $existingEmailTemplate->companies_id;
        $request['apps_id'] = $existingEmailTemplate->apps_id;
        $request['name'] = $existingEmailTemplate->name . '-' . $randomInstance;
        $request['template'] = $existingEmailTemplate->template;

        //try to save all the fields we allow
        if ($this->model->save($request, $this->createFields)) {
            return $this->response($this->model->toArray());
        } else {
            //if not thorw exception
            throw new UnprocessableEntityHttpException((string) current($this->model->getMessages()));
        }
    }
}
