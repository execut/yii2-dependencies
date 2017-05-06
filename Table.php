<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 3/9/17
 * Time: 3:06 PM
 */

namespace execut\dependencies;


use yii\base\Component;
use yii\base\Exception;

class Table extends Component
{
    public $idField = 'id';
    public $tableName = null;
    public $nameField = 'name';
    public $isCreateForeignKey = true;
    public $modelClass = null;
    public $route = null;
    public $fixture = null;
    public $relationsKeys = [];
    public function getRelationKey($key) {
        if (empty($this->relationsKeys[$key])) {
            throw new Exception('Relation key ' . $key . ' for table ' . $this->tableName . ' not found');
        }

        return $this->relationsKeys[$key];
    }
}