<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\AccessList;
use Phalcon\Http\Response;
use Phalcon\Acl\Role;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Gewaer\Models\Apps;
use Gewaer\Exception\NotFoundHttpException;
use Gewaer\Exception\ServerErrorHttpException;
use Gewaer\Models\Roles;

/**
 * Class RolesController
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
class RolesAccesListController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = [];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = [];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new AccessList();

        //get the list of roes for the systema + my company
        $this->additionalSearchFields = [
            ['is_deleted', ':', 0],
            ['apps_id', ':', $this->app->getId()],
        ];
    }

    /**
     * Add a new item
     *
     * @method POST
     * @url /v1/roles-acceslist
     *
     * @return Phalcon\Http\Response
     */
    public function create() : Response
    {
        $request = $this->request->getPost();

        if (empty($request)) {
            $request = $this->request->getJsonRawBody(true);
        }

        //Ok let validate user password
        $validation = new Validation();
        $validation->add('roles', new PresenceOf(['message' => _('Role information is required.')]));
        $validation->add('access', new PresenceOf(['message' => _('Access list is required.')]));

        //validate this form for password
        $messages = $validation->validate($request);
        if (count($messages)) {
            foreach ($messages as $message) {
                throw new ServerErrorHttpException((string)$message);
            }
        }

        //set the company and app
        $this->acl->setCompany($this->userData->DefaultCompany);
        $this->acl->setApp($this->app);

        $scope = 1;
        //create the role , the scope is level 1 , that means user
        $this->acl->addRole(new Role($request['roles']['name'], $request['roles']['description']), $scope);

        /**
         * we always deny permision, by default the canvas set allow to all
         * so we only have to take away permissions
         */
        foreach ($request['access'] as $access) {
            $this->acl->deny($request['roles']['name'], $access['resources_name'], $access['access_name']);
        }

        return $this->response($request['roles']);
    }

    /**
     * get item
     *
     * @param mixed $id
     *
     * @method GET
     * @url /v1/roles-acceslist/{id}
     *
     * @return Phalcon\Http\Response
     */
    public function getById($id) : Response
    {
        //find the info
        $objectInfo = $this->model->findFirst([
            'roles_name = ?0 AND is_deleted = 0 AND apps_id in (?1, ?2)',
            'bind' => [$id, $this->app->getId(), Apps::GEWAER_DEFAULT_APP_ID],
        ]);

        //get relationship
        if ($this->request->hasQuery('relationships')) {
            $relationships = $this->request->getQuery('relationships', 'string');

            $objectInfo = QueryParser::parseRelationShips($relationships, $objectInfo);
        }

        if ($objectInfo) {
            return $this->response($objectInfo);
        } else {
            throw new Exception('Record not found');
        }
    }

    /**
     * Update a new Entry
     *
     * @method PUT
     * @url /v1/roles-acceslist/{id}
     *
     * @return Phalcon\Http\Response
     */
    public function edit($id) : Response
    {
        if (!$role = Roles::findFirst($id)) {
            throw new NotFoundHttpException('Record not found');
        }

        $request = $this->request->getPut();

        if (empty($request)) {
            $request = $this->request->getJsonRawBody(true);
        }

        //Ok let validate user password
        $validation = new Validation();
        $validation->add('roles', new PresenceOf(['message' => _('Role information is required.')]));
        $validation->add('access', new PresenceOf(['message' => _('Access list is required.')]));

        //validate this form for password
        $messages = $validation->validate($request);
        if (count($messages)) {
            foreach ($messages as $message) {
                throw new ServerErrorHttpException((string)$message);
            }
        }

        //set the company and app
        $this->acl->setCompany($this->userData->DefaultCompany);
        $this->acl->setApp($this->app);

        $role->name = $request['roles']['name'];
        $role->description = $request['roles']['description'];
        if (!$role->update()) {
            throw new ServerErrorHttpException((string) current($role->getMessages()));
        }

        //delete the acces list before hand
        AccessList::deleteAllByRole($role);

        /**
         * we always deny permision, by default the canvas set allow to all
         * so we only have to take away permissions
         */
        foreach ($request['access'] as $access) {
            $this->acl->deny($request['roles']['name'], $access['resources_name'], $access['access_name']);
        }

        return $this->response($role);
    }

    /**
     * delete a new Entry
     *
     * @method DELETE
     * @url /v1/roles-acceslist/{id}
     *
     * @return Phalcon\Http\Response
     */
    public function delete($id) : Response
    {
        if ($role = Roles::findFirst($id)) {
            if ($this->softDelete == 1) {
                $role->softDelete();
            } else {
                //delete the acces list before hand
                AccessList::deleteAllByRole($role);

                $role->delete();
            }

            return $this->response(['Delete Successfully']);
        } else {
            throw new NotFoundHttpException('Record not found');
        }
    }
}
