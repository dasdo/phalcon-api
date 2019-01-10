<?php

declare(strict_types=1);

namespace Gewaer\Traits;

use Phalcon\Di;
use Phalcon\Http\Response;
use Phalcon\Validation;
use Phalcon\Validation\Validator\File as FileValidator;
use Gewaer\Exception\UnprocessableEntityHttpException;
use Gewaer\Models\FileSystem;
use Gewaer\Filesystem\Helper;

/**
 * Trait ResponseTrait
 *
 * @package Gewaer\Traits
 *
 * @property Users $user
 * @property AppsPlans $appPlan
 * @property CompanyBranches $branches
 * @property Companies $company
 * @property UserCompanyApps $app
 * @property \Phalcon\Di $di
 *
 */
trait FileManagementTrait
{
    /**
     * Get item.
     *
     * @method GET
     * url /v1/filesystem/{id}
     *
     * @param mixed $id
     *
     * @return \Phalcon\Http\Response
     * @throws Exception
     */
    public function getById($id) : Response
    {
        //find the info
        $records = $this->model->findFirst([
            'conditions' => 'entity_id = ?0 and companies_id = ?1 and apps_id = ?2',
            'bind' => [$id, $this->userData->currentCompanyId(), $this->app->getId()]
        ]);

        if (!is_object($records)) {
            throw new UnprocessableEntityHttpException('Records not found');
        }

        return $this->response($records);
    }

    /**
     * Add a new item.
     *
     * @method POST
     * url /v1/filesystem
     *
     * @return \Phalcon\Http\Response
     * @throws Exception
     */
    public function create() : Response
    {
        if (!$this->request->hasFiles()) {
            //@todo handle base64 images
        }

        return $this->response($this->processFiles());
    }

    /**
     * Update an item.
     *
     * @method PUT
     * url /v1/filesystem/{id}
     *
     * @param mixed $id
     *
     * @return \Phalcon\Http\Response
     * @throws Exception
     */
    public function edit($id) : Response
    {
        $file = $this->model->findFirst([
                'conditions' => 'id = ?0 and companies_id = ?1 and apps_id = ?2',
                'bind' => [$id, $this->userData->currentCompanyId(), $this->app->getId()]
            ]);

        if (!is_object($file)) {
            throw new UnprocessableEntityHttpException('Record not found');
        }

        $request = $this->request->getPut();

        if (empty($request)) {
            $request = $this->request->getJsonRawBody(true);
        }
        
        $systemModule = $request['system_modules'] ?? 0;
        $entityId = $request['entity_id'] ?? 0;

        $file->system_modules_id = $systemModule;
        $file->entity_id = $entityId;

        if (!$file->update()) {
            throw new UnprocessableEntityHttpException((string)current($file->getMessages()));
        }

        return $this->response($file);
    }

    /**
     * Set the validation for the files
     *
     * @return Validation
     */
    protected function validation(): Validation
    {
        $validator = new Validation();

        $uploadConfig = [
            'maxSize' => '10M',
            'messageSize' => ':field exceeds the max filesize (:max)',
            'allowedTypes' => [
                'image/jpeg',
                'image/png',
            ],
            'messageType' => 'Allowed file types are :types',
        ];

        $validator->add(
            'file',
            new FileValidator($uploadConfig)
        );

        return $validator;
    }

    /**
     * Upload the document and save them to the filesystem
     *
     * @param object? $fileObject
     * @return array
     */
    protected function processFiles(): array
    {
        //@todo validate entity id
        $systemModule = $this->request->getPost('system_modules', 'int', '0');
        $entityId = $this->request->getPost('entity_id', 'int', '0');

        $validator = $this->validation();

        $files = [];
        foreach ($this->request->getUploadedFiles() as $file) {
            //validate this current file
            $errors = $validator->validate(['file' => [
                'name' => $file->getName(),
                'type' => $file->getType(),
                'tmp_name' => $file->getTempName(),
                'error' => $file->getError(),
                'size' => $file->getSize(),
            ]]);

            if (count($errors)) {
                foreach ($errors as $error) {
                    throw new UnprocessableEntityHttpException((string)$error);
                }
            }

            $filePath = Helper::generateUniqueName($file, $this->config->filesystem->local->path);
            $compleFilePath = $this->config->filesystem->local->path . $filePath;

            $this->di->get('filesystem', 'local')->writeStream($filePath, fopen($file->getTempName(), 'r'));

            $fileSystem = new FileSystem();
            $fileSystem->name = $file->getName();
            $fileSystem->system_modules_id = $systemModule;
            $fileSystem->entity_id = $entityId;
            $fileSystem->companies_id = $this->userData->currentCompanyId();
            $fileSystem->apps_id = $this->app->getId();
            $fileSystem->users_id = $this->userData->getId();
            $fileSystem->path = $compleFilePath;
            $fileSystem->url = $compleFilePath;
            $fileSystem->size = $file->getSize();

            if (!$fileSystem->save()) {
                throw new UnprocessableEntityHttpException((string)current($fileSystem->getMessages()));
            }

            $files[] = $fileSystem;
        }

        return $files;
    }
}
