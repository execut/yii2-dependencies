<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 3/22/17
 * Time: 4:02 PM
 */

namespace execut\dependencies;

class Behavior extends \yii\base\Behavior
{
    public $dependenciesClass = null;
    public function getRelationKey($key) {
        $table = $this->getDependencies();


        return $table->getRelationKey($key);
    }

    public function getTableName() {
        return $this->getDependencies()->getTable($this->tableName)->tableName;
    }

    /**
     * @return mixed
     */
    public function getDependencies()
    {
        /**
         * @var \detalika\goods\OuterDependencies $outerDep
         */
        $outerDep = \yii::$container->get($this->dependenciesClass);
        $table = $outerDep->getTable($this->tableName);
        return $table;
    }
}