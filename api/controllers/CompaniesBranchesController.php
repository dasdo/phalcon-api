<?php

declare(strict_types=1);

namespace Gewaer\Api\Controllers;

use Phalcon\Http\Response;
use Gewaer\Exception\UnprocessableEntityHttpException;
use Baka\Http\QueryParser;
use Gewaer\Models\CompaniesBranches;

/**
 * Class CompaniesController
 *
 * @package Gewaer\Api\Controllers
 *
 * @property Users $userData
 * @property Request $request
 */
class CompaniesBranchesController extends BaseController
{
    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $createFields = ['name', 'description', 'is_default'];

    /*
     * fields we accept to create
     *
     * @var array
     */
    protected $updateFields = ['name', 'description', 'is_default'];

    /**
     * set objects
     *
     * @return void
     */
    public function onConstruct()
    {
        $this->model = new CompaniesBranches();
        $this->model->users_id = $this->userData->getId();
        $this->model->companies_id = $this->userData->default_company;

        $this->additionalSearchFields = [
            ['companies_id', ':', $this->userData->default_company],
        ];
    }

    /**
     * Get Uer
     *
     * @param mixed $id
     *
     * @method GET
     * @url /v1/company/{id}
     *
     * @return Response
     */
    public function getById($id) : Response
    {
        //find the info
        $company = $this->model->findFirst([
            'id = ?0 AND is_deleted = 0 and companies_id = ?1',
            'bind' => [$id, $this->userData->default_company],
        ]);

        //get relationship
        if ($this->request->hasQuery('relationships')) {
            $relationships = $this->request->getQuery('relationships', 'string');

            $company = QueryParser::parseRelationShips($relationships, $company);
        }

        if ($company) {
            return $this->response($company);
        } else {
            throw new UnprocessableEntityHttpException('Record not found');
        }
    }

    /**
     * Add a new item
     *
     * @method POST
     * @url /v1/company
     *
     * @return Response
     */
    public function create() : Response
    {
        $request = $this->request->getPost();

        if (empty($request)) {
            $request = $this->request->getJsonRawBody(true);
        }

        //transaction
        $this->db->begin();

        //try to save all the fields we allow
        if ($this->model->save($request, $this->createFields)) {
            $this->db->commit();
            return $this->response($this->model->toArray());
        } else {
            $this->db->rollback();
            throw new UnprocessableEntityHttpException((string) $this->model->getMessages()[0]);
        }
    }

    /**
     * Update a User Info
     *
     * @method PUT
     * @url /v1/company/{id}
     *
     * @return Response
     */
    public function edit($id) : Response
    {
        $company = $this->model->findFirst([
            'id = ?0 AND is_deleted = 0 and companies_id = ?1',
            'bind' => [$id, $this->userData->default_company],
        ]);

        if ($company) {
            $request = $this->request->getPut();

            if (empty($request)) {
                $request = $this->request->getJsonRawBody(true);
            }

            //update
            if ($company->update($request, $this->updateFields)) {
                return $this->response($company);
            } else {
                //didnt work
                throw new UnprocessableEntityHttpException((string) current($company->getMessages()));
            }
        } else {
            throw new UnprocessableEntityHttpException('Record not found');
        }
    }
}
