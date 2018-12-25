<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Gewaer\Models\AccessList;
use Phalcon\Http\Response;

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
     * @url /v1/business
     *
     * @return Phalcon\Http\Response
     */
    public function create() : Response
    {
        $request = $this->request->getPost();

        if (empty($request)) {
            $request = $this->request->getJsonRawBody(true);
        }

        //try to save all the fields we allow
        if ($this->model->save($request, $this->createFields)) {
            return $this->response($this->model->toArray());
        } else {
            //if not thorw exception
            throw new Exception($this->model->getMessages()[0]);
        }
    }

    /**
     * get item
     *
     * @param mixed $id
     *
     * @method GET
     * @url /v1/business/{id}
     *
     * @return Phalcon\Http\Response
     */
    public function getById($id) : Response
    {
        //find the info
        $objectInfo = $this->model->findFirst([
            'roles_name = ?0 AND is_deleted = 0',
            'bind' => [$id],
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
     * @url /v1/business/{id}
     *
     * @return Phalcon\Http\Response
     */
    public function edit($id) : Response
    {
        if ($objectInfo = $this->model->findFirst($id)) {
            $request = $this->request->getPut();

            if (empty($request)) {
                $request = $this->request->getJsonRawBody(true);
            }
            //update
            if ($objectInfo->update($request, $this->updateFields)) {
                return $this->response($objectInfo->toArray());
            } else {
                //didnt work
                throw new Exception($objectInfo->getMessages()[0]);
            }
        } else {
            throw new Exception('Record not found');
        }
    }

    /**
     * delete a new Entry
     *
     * @method DELETE
     * @url /v1/business/{id}
     *
     * @return Phalcon\Http\Response
     */
    public function delete($id) : Response
    {
        if ($objectInfo = $this->model->findFirst($id)) {
            if ($this->softDelete == 1) {
                $objectInfo->softDelete();
            } else {
                $objectInfo->delete();
            }

            return $this->response(['Delete Successfully']);
        } else {
            throw new Exception('Record not found');
        }
    }
}
