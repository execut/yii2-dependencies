<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 3/9/17
 * Time: 2:54 PM
 */

namespace execut\dependencies;


use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

abstract class Outer extends Component
{
    public $defaultTablesConfig = [];
    public $tablesConfig = [];
    protected $tables = null;

    /**
     * @param $tableName
     * @return Table
     * @throws Exception
     */
    public function getTable($tableName) {
        if ($this->tables === null) {
            $this->initTables();
        }

        if (!isset($this->tables[$tableName])) {
            throw new Exception('Table ' . $tableName . ' not found');
        }

        return $this->tables[$tableName];
    }

    public function initTables() {
        $tablesObjects = [];
        $tablesConfigs = ArrayHelper::merge($this->defaultTablesConfig, $this->tablesConfig);
        foreach ($tablesConfigs as $key => $table) {
            if (is_array($table)) {
                if (!isset($table['class'])) {
                    $table['class'] = Table::className();
                }

                $table = \yii::createObject($table);
                $tablesObjects[$key] = $table;
            }
        }

        $this->tables = $tablesObjects;

        return $this;
    }

    /**
     * @return self
     */
    public static function getInstance() {
        $dependenciesClass = self::className();
        $outerDep = \yii::$container->get($dependenciesClass);

        return $outerDep;
    }

    public static function getRelationKey($modelClass, $key) {
        $instance = self::getInstance();

        return $instance->getTable($modelClass)->getRelationKey($key);
    }

    public static function getTableName($modelClass) {
        $instance = self::getInstance();
        $table = $instance->getTable($modelClass);

        return $table->tableName;
    }
}