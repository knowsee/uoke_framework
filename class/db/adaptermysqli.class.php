<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

/**
 * Mysqli数据库引擎适配器
 * @author Knowsee
 */
class db_AdapterMysqli implements Adapter_db {

    private $link = NULL;
    private $config = array();
    private $sql = array();
    private $table = '';
    private $sqlAdv = array('order' => '',
        'groupby' => '',
        'having' => '',
        'limit' => '',
    );
    private $queryId = array();

    public function __construct($config) {
        if (!empty($config)) {
            $this->config = $config;
        }
        if (!$this->link) {
            $this->link = new mysqli($config['host'], $config['dbuser'], $config['password'], $config['dbname']);
            try {
                if ($this->link->connect_errno) {
                    throw new qunkenException('Mysql Host Can\'t Connect', $this->link->connect_errno, array('version' => $this->getVersion(), 'drive' => 'mysqli', 'sql' => 'exc connect to mysql server'));
                }
                $this->link->set_charset($config['dbcharset']);
            } catch (qunkenException $e) {
                return false;
            }
        }
        return $this;
    }

    public function table($tableName) {
        $this->table = '`' . $this->config['db_pre'] . $tableName . '`';
        return $this;
    }

    public function getOne() {
        $sqlExc = $this->handleEasySql();
        $sql = sprintf('SELECT * FROM %s WHERE %s', $this->table, $sqlExc['where']);
        return $this->query($sql)->fetch_assoc();
    }

    public function getList($key = '', $returnType = 'string') {
        $sqlExc = $this->handleEasySql();
        $sql = sprintf('SELECT * FROM %s WHERE %s %s %s %s %s', $this->table, $sqlExc['where'], $sqlExc['order'], $sqlExc['groupby'], $sqlExc['having'], $sqlExc['limit']);
        if ($this->queryId) {
            $this->numCols = $this->numRows = 0;
            $this->queryId = null;
        }
        $this->queryId = $this->query($sql);
        if ($this->link->more_results()) {
            while (($res = $this->link->next_result()) != NULL) {
                $res->free_result();
            }
        }
        if (false ===! $this->queryId) {
            $this->numRows = $this->queryId->num_rows;
            $this->numCols = $this->queryId->field_count;
            return array($this->numRows, $this->getAll($key, $returnType));
        }
    }
    
    private function getAll($key, $returnType) {
        $result = array();
        if ($this->numRows > 0) {
            for ($i = 0; $i < $this->numRows; $i++) {
                $fetcharray = $this->queryId->fetch_assoc();
                if ($key && $returnType == 'string') {
                    $result[$fetcharray[$key]] = $fetcharray;
                } elseif ($key && $returnType == 'array') {
                    $result[$fetcharray[$key]][] = $fetcharray;
                } else {
                    $result[$i] = $fetcharray;
                }
            }
            $this->queryId->data_seek(0);
        }
        return $result;
    }

    public function getInsetLastId() {
        return $this->link->insert_id;
    }

    public function getFieldAny($field) {
        $sqlExc = $this->handleEasySql();
        $sql = sprintf('SELECT %s FROM %s WHERE %s', helper_db::field($field), $this->table, $sqlExc['where']);
        return $this->query($sql)->fetch_assoc();
    }

    public function getFieldCount($field, $countType) {
        $sqlExc = $this->handleEasySql();
        $sql = sprintf('SELECT %s FROM %s WHERE %s', helper_db::fieldType($field, $countType), $this->table, $sqlExc['where']);
        return $this->query($sql)->fetch_assoc();
    }

    public function getVersion() {
        return $this->link ? $this->link->server_version : 'unknow';
    }

    public function insert($data, $return_insert_id = false, $replace = false) {
        $sql = sprintf('%s %s SET %s', $replace ? 'REPLACE INTO' : 'INSERT INTO', $this->table, helper_db::arrayToSql($data));
        $return = $this->query($sql);
        return $return_insert_id ? $this->getInsetLastId() : $return;
    }

    public function insertReplace($data, $affected = false) {
        $sql = sprintf('INSERT IGNORE INTO %s SET %s', $this->table, helper_db::arrayToSql($data));
        $return = $this->query($sql);
        return $affected ? $this->link->affected_rows : $return;
    }

    public function insertMulti($key, $data, $replace = false) {
        foreach ($key as $k => $value) {
            $fkey[] = "`$value`";
        }
        $sql = '(' . implode(',', $fkey) . ')';
        $sql = $sql . ' VALUES ';
        foreach ($data as $k => $value) {
            $ky = array();
            foreach ($value as $vk => $vvalue) {
                $ky[] = "'$vvalue'";
            }
            $kkey[$k] = '(' . implode(',', $ky) . ')';
        }
        $data = $sql . implode(',', $kkey);
        $sql = sprintf('%s %s SET %s', $replace ? 'REPLACE INTO' : 'INSERT INTO', $this->table, $data);
        return $this->query($sql);
    }

    public function update($data, $longWait = false) {
        $sqlExc = $this->handleEasySql();
        $sql = sprintf('%s %s SET %s WHERE %s', 'UPDATE' . ($longWait ? 'LOW_PRIORITY' : ''), $this->table, helper_db::arrayToSql($data), $sqlExc['where']);
        return $this->query($sql);
    }

    public function delete() {
        $sqlExc = $this->handleEasySql();
        $sql = sprintf('DELETE FROM %s WHERE %s', $this->table, $sqlExc['where']);
        return $this->query($sql);
    }

    public function query($sql) {
        if (DEBUGSET !== 0) {
            $debug['sql'] = $sql;
            $debug['begin'] = microtime(true);
        }
        try {
            Factory_Db::saveRunSql($sql);
            $result = $this->link->query($sql);
            if ($this->link->error) {
                throw new qunkenException($this->link->error, $this->link->errno, array('version' => $this->getVersion(), 'drive' => 'mysqli', 'sql' => $sql));
            }
            if (DEBUGSET !== 0) {
                $debug['end'] = microtime(true);
                $debug['time'] = '[ RunTime:' . floatval($debug['end'] - $debug['begin']) . 's ]';
                if (is_object($this->link->query("explain $sql")))
                    $debug['debugsql'] = $this->link->query("explain $sql")->fetch_assoc();
                Factory_Db::setDebugSql($debug);
            }
        } catch (qunkenException $e) {
            return false;
        }
        return $result;
    }

    public function order($array) {
        if (!is_array($array) || !$array)
            return '';
        foreach ($array as $key => $value) {
            $order[] = "$key $value";
        }
        $this->sqlAdv['order'] = $order ? 'ORDER BY ' . implode(',', $order) : '';
        return $this;
    }

    public function where($array) {
        $this->sql[] = implode(' AND ', helper_db::handlesql($array));
        return $this;
    }

    public function limit($array) {
        if (!is_array($array) || !$array)
            return '';
        $this->sqlAdv['limit'] = 'LIMIT ' . implode(',', $array);
        return $this;
    }

    public function whereOr($array) {
        $this->sql[] = '(' . implode(' OR ', helper_db::handlesql($array)) . ')';
        return $this;
    }

    public function groupBy($array) {
        $this->sqlAdv['groupby'] = $array ? 'GROUP BY ' . implode(',', $array) : '';
        return $this;
    }

    public function havingBy($array) {
        $this->sqlAdv['having'] = 'HAVING ' . helper_db::handlesql($array);
        return $this;
    }

    private function handleEasySql() {
        $sql = '';
        if ($this->sql) {
            $sql = implode(' AND ', $this->sql);
        }
        $returnSqlCount = array('where' => !$sql ? '1' : $sql, 'groupby' => $this->sqlAdv['groupby'], 'having' => $this->sqlAdv['having'], 'limit' => $this->sqlAdv['limit'], 'order' => $this->sqlAdv['order']);
        $this->sql = $this->sqlAdv = array();
        return $returnSqlCount;
    }

}
