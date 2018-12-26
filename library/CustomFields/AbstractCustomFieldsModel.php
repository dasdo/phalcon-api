<?php
declare(strict_types=1);

namespace Gewaer\CustomFields;

use Gewaer\Models\Modules;

/**
 * Custom Fields Abstract Class
 * @property \Phalcon\Di $di
 */
abstract class AbstractCustomFieldsModel extends \Baka\Database\ModelCustomFields
{
    /**
     * Get all custom fields of the given object
     *
     * @param  array  $fields
     * @return \Phalcon\Mvc\Model
     */
    public function getAllCustomFields(array $fields = [])
    {
        //We does it only find names in plural? We need to fix this or make a workaroun
        if (!$models = Modules::findFirstByName($this->getSource())) {
            return;
        }

        $fieldsIn = null;

        if (!empty($fields)) {
            $fieldsIn = " and name in ('" . implode("','", $fields) . ')';
        }

        $conditions = 'modules_id = ? ' . $fieldsIn;

        $bind = [$this->getId(), $this->di->getApp()->getId(), $models->getId(), $this->di->getUserData()->default_company];

        // $customFieldsValueTable = $this->getSource() . '_custom_fields';
        $customFieldsValueTable = $this->getSource() . '_custom_fields';

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
                                          AND c.companies_id = ? 
                                          AND l.companies_id = c.companies_id");

        $result->execute($bind);

        // $listOfCustomFields = $result->fetchAll();
        $listOfCustomFields = [];

        while ($row = $result->fetch(\PDO::FETCH_OBJ)) {
            $listOfCustomFields[$row->name] = $row->value;
        }

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
        if ($fields = \Gewaer\CustomFields\CustomFields::findByModulesId($module->id)->toArray()) {
            foreach ($fields as $field) {
                array_push($allFields, $field['name']);
            }
            return $allFields;
        }
    }
}
