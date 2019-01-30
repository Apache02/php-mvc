<?php

namespace fw\db;

use \fw\AppComponent;
use \fw\db\ActiveQuery;


abstract class ActiveRecord
{
    /** @var \fw\db\DbConnection */
    public static $db = null;
    private $_attributes = [];
    private $_errors = [];


    public function __construct()
    {
        $this->reset();
    }

    /**
     * @return string name of table
     */
    abstract public static function tableName();

    /**
     * @return array list of columns
     */
    abstract public static function tableColumns();

    /**
     * @return string primary key column name
     */
    abstract public static function tablePk();

    /**
     * @return array
     */
    abstract public function getSafeAttributes();

    public function getPrimaryKeyValue()
    {
        $pk = static::tablePk();
        return $this->$pk;
    }

    /**
     * @param $attributes
     * @return bool
     */
    public function load($attributes)
    {
        $this->_attributes = $attributes;
        return true;
    }

    public function reset()
    {
        $this->_attributes = [];
        $this->_errors = [];
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $attributes = [];
        foreach (static::tableColumns() as $columnName) {
            $attributes[$columnName] = $this->$columnName;
        }
        return $attributes;
    }

    /**
     * @return array
     */
    public function getDirtyAttributes()
    {
        return $this->_attributes;
    }


    /**
     * @param $attribute
     * @param $text
     */
    public function addError($attribute, $text)
    {
        if (!isset($this->_errors[$attribute])) {
            $this->_errors[$attribute] = [];
        }
        $this->_errors[$attribute][] = $text;
    }

    /**
     * @param string|null $attribute
     * @return array|null
     */
    public function getError($attribute = null)
    {
        if ($attribute !== null) {
            return $this->_errors[$attribute][0] ?? null;
        }
        return $this->_errors;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        $list = [];
        foreach ($this->_errors as $attribute => $errors) {
            foreach ($errors as $text) {
                $list[] = $text;
            }
        }
        return $list;
    }

    /**
     * @param string|null $attribute
     * @return bool
     */
    public function hasErrors($attribute = null)
    {
        if ($attribute === null) {
            return $this->_errors !== [];
        }
        return isset($this->_errors[$attribute]);
    }


    /**
     * @return bool
     */
    abstract public function validate();

    public function getIsNewRecord()
    {
        return empty($this->getPrimaryKeyValue());
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        if ($this->getIsNewRecord()) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    public function insert()
    {
        $attributes = $this->getAttributes();
        $pk = static::tablePk();
        unset($attributes[$pk]);

        $db = static::$db;
        if ($db->insert(static::tableName(), $attributes)) {
            $this->$pk = $db->lastInsertId();
            return true;
        }
        return false;
    }

    public function update()
    {
        $attributes = $this->getAttributes();
        $pk = static::tablePk();
        unset($attributes[$pk]);
        $pkValue = (int)$this->getPrimaryKeyValue();

        $db = static::$db;
        if ($db->update(static::tableName(), $attributes, " `$pk` = $pkValue")) {
            return true;
        }
        return false;
    }

    /**
     * @return \fw\db\ActiveQuery
     */
    public static function find ()
    {
        return new ActiveQuery(static::$db, static::class);
    }

    /**
     * @param $id
     * @return ActiveRecord
     */
    public static function findOne ( $id )
    {
        return self::find()->byPk($id)->one();
    }

}
