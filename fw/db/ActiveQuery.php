<?php

namespace fw\db;


class ActiveQuery
{
    public $select = '*';
    public $where = [];
    public $order = null;
    public $limit = null;
    public $offset = 0;

    public $params = [];


    /** @var \fw\db\DbConnection */
    private $db = null;

    private $activeRecordClass = null;


    public function __construct($db, $activeRecordClass)
    {
        $this->db = $db;
        $this->activeRecordClass = $activeRecordClass;
    }

    /**
     * @param $columns array|string
     * @return $this
     */
    public function select($columns)
    {
        $this->select = $columns;
        return $this;
    }

    private function mergeParams($params)
    {
        if ($params) {
            $this->params = array_merge($this->params, $params);
        }
    }

    public function where($condition, $params = null)
    {
        $this->where = [];
        return $this->andWhere($condition, $params);
    }

    public function andWhere($condition, $params = null)
    {
        $this->where[] = $condition;
        $this->mergeParams($params);
        return $this;
    }

    public function orderBy($order)
    {
        $this->order = $order;
        return $this;
    }

    public function limit ( $offset, $limit )
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }



    protected function prepareColumnsPart()
    {
        $columns = $this->select;
        if (is_array($columns)) {
            $columns = implode(',', $columns);
        }
        return $columns;
    }

    protected function prepareWherePart($params = null)
    {
        $this->mergeParams($params);
        $items = array_map(function ($part) {
            return "( $part )";
        }, $this->where);
        return implode(" AND ", $items);
    }

    protected function prepareOrderPart()
    {
        if (empty($this->order)) {
            return '';
        }
        $order = [];
        foreach ($this->order as $attribute => $sortDirection) {
            if (is_numeric($attribute)) {
                $attribute = $sortDirection;
                $sortDirection = '';
            }
            switch ($sortDirection) {
                case SORT_ASC:
                case 'ASC':
                case 'asc':
                    $order[] = "`$attribute` ASC";
                    break;
                case SORT_DESC:
                case 'DESC':
                case 'desc':
                    $order[] = "`$attribute` DESC";
                    break;
                case '':
                    $order[] = $attribute;
            }
        }
        return implode(',', $order);
    }


    private function queryInternal($method)
    {
        $activeRecordClass = $this->activeRecordClass;
        $tableName = $activeRecordClass::tableName();

        $columns = $this->prepareColumnsPart();
        $where = $this->prepareWherePart();

        $sql = [
            "SELECT $columns",
            "FROM `$tableName` `t`",
        ];
        if ( $where ) {
            $sql[] = "WHERE $where";
        }
        $order = $this->prepareOrderPart();
        if ($order) {
            $sql[] = "ORDER BY $order";
        }

        $limit = (int)$this->limit;
        $offset = (int)$this->offset;
        if ($limit || $offset) {
            $sql[] = "LIMIT {$offset}, {$limit}";
        }

        $sql = implode(" \n", $sql);
        $statement = $this->db->getPdo()->prepare($sql);
        $statement->execute($this->params);
        $result = $statement->$method(\PDO::FETCH_ASSOC);
        return $result;
    }


    private function populateRecord($row)
    {
        $activeRecordClass = $this->activeRecordClass;
        $model = new $activeRecordClass();
        foreach ($row as $attributeName => $value) {
            $model->$attributeName = $value;
        }
        return $model;
    }

    public function all()
    {
        $rows = $this->queryInternal('fetchAll');
        return array_map([$this, 'populateRecord'], $rows);
    }

    public function one()
    {
        $this->limit = 1;
        $this->offset = 0;
        $row = $this->queryInternal('fetch');
        if (!$row) {
            return null;
        }
        return $this->populateRecord($row);
    }

    public function byPk($id)
    {
        $activeRecordClass = $this->activeRecordClass;
        $pk = $activeRecordClass::tablePk();
        $this->andWhere("$pk = :_pk", [':_pk' => $id]);
        return $this;
    }

    public function count ()
    {
        $activeRecordClass = $this->activeRecordClass;
        $tableName = $activeRecordClass::tableName();

        $where = $this->prepareWherePart();

        $sql = [
            "SELECT COUNT(*)",
            "FROM `$tableName` `t`",
        ];
        if ( $where ) {
            $sql[] = "WHERE $where";
        }

        $sql = implode(" \n", $sql);
        $statement = $this->db->getPdo()->prepare($sql);
        $statement->execute($this->params);
        $result = $statement->fetch(\PDO::FETCH_COLUMN);
        return $result;
    }

}
