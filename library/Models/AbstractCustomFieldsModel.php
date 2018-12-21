<?php
declare(strict_types=1);

namespace Gewaer\Models;

abstract class AbstractCustomFieldsModel extends \Baka\Database\ModelCustomFields
{
    /**
     * Get all custom fields of the given object
     *
     * @param  array  $fields
     * @return \Phalcon\Mvc\Model
     */
    public function getAllCustomFields(array $fields = [], int $company_id = 0)
    {
        //We does it only find names in plural? We need to fix this or make a workaroun
        if (!$models = Modules::findFirstByName($this->getSource())) {
            return;
        }

        print_r($models->toArray());
        die();

        $conditions = [];
        $fieldsIn = null;

        if (!empty($fields)) {
            $fieldsIn = " and name in ('" . implode("','", $fields) . ')';
        }

        $conditions = 'modules_id = ? ' . $fieldsIn;

        $bind = [$this->getId(), $models->getId(), $this->di->getUserData()->default_company, $this->di->getApp()->getId()];

        // $customFieldsValueTable = $this->getSource() . '_custom_fields';
        $customFieldsValueTable = $this->getSource() . '_custom_fields';

        print_r($customFieldsValueTable);
        die();

        //We are to make a new query to replace old gewaer implementation.
        $result = $this->getReadConnection()->prepare("SELECT l.{$this->getSource()}_id,
                                               c.id as field_id,
                                               c.name,
                                               l.value ,
                                               c.users_id,
                                               l.created_at,
                                               l.updated_at
                                        FROM {$customFieldsValueTable} l,
                                             custom_fields c
                                        WHERE c.id = l.custom_fields_id
                                          AND l.{$this->getSource()}_id = ?
                                          AND c.apps_id = ?
                                          AND c.modules_id = ?
                                          AND c.company_id = ? 
                                          AND l.company_id = c.company_id");

        $result->execute($bind);

        // $listOfCustomFields = $result->fetchAll();
        $listOfCustomFields = [];

        while ($row = $result->fetch(\PDO::FETCH_OBJ)) {
            $listOfCustomFields[$row->name] = $row->value;
        }

        print_r($listOfCustomFields);
        die();

        return $listOfCustomFields;
    }

    /**
     * Get all custom fields of the given model
     *
     * @param  array  $fields
     * @return \Phalcon\Mvc\Model
     */
    public function getCustomFieldsByModel($modelName)
    {
        if (!$module = Modules::findFirstByName($modelName)) {
            return;
        }
        $allFields = [];
        if ($fields = \Incursio\Models\CustomFields::findByModulesId($module->id)->toArray()) {
            foreach ($fields as $field) {
                array_push($allFields, $field['name']);
            }
            return $allFields;
        }
    }
}
