<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Data;

use byteShard\Environment;
use byteShard\Internal\Data;
use byteShard\Exception;
use byteShard\Enum;
use byteShard\Locale;
use byteShard\Popup\Message;
use byteShard\Database;
use stdClass;

class Insert extends Data
{
    private array $queries = [];

    /**
     * @return array
     * @throws Exception
     */
    public function process(): array
    {
        $msg = new Message('');
        $msg->setLabel(Locale::get('byteShard.data.insert.failed.label'));
        if ($this->cell->getAccessType() !== Enum\AccessType::RW) {
            $msg->setMessage(Locale::get('byteShard.data.insert.failed.permission'));
            return $this->getErrorResult($msg->getNavigationArray());
        }
        if (count($this->table) === 0) {
            $msg->setMessage(Locale::get('byteShard.data.insert.failed.table_not_defined'));
            return $this->getErrorResult($msg->getNavigationArray());
        }
        if ($this->checkClientData($msg) === false) {
            return $this->getErrorResult($msg->getNavigationArray());
        }
        if ($this->checkUnique($msg) === false) {
            return $this->getErrorResult($msg->getNavigationArray());
        }
        if ($this->buildQuery() === false) {
            $msg->setMessage(Locale::get('byteShard.data.insert.failed.query'));
            return $this->getErrorResult($msg->getNavigationArray());
        }
        if ($this->connect($msg) === true) {
            if ($this->insertData() === false) {
                $msg->setMessage(Locale::get('byteShard.data.insert.failed.insert'));
                $this->disconnect();
                return $this->getErrorResult($msg->getNavigationArray());
            }
            $this->disconnect();
        } else {
            return $this->getErrorResult($msg->getNavigationArray());
        }
        $this->success = true;
        return $this->getSuccessResult();
    }

    /**
     * whether to store the user_id and creation time
     *
     * @param bool $bool
     * @return $this
     */
    public function useCreateLog(bool $bool = true): self
    {
        $this->useCreateLog = $bool;
        return $this;
    }

    /**
     * whether the archive field must be set by insert
     *
     * @param bool $bool
     * @return $this
     */
    public function useArchiveLog(bool $bool = true): self
    {
        $this->useArchiveLog = $bool;
        return $this;
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function insertData(): bool
    {
        global $dbDriver;
        if ($dbDriver === Environment::DRIVER_MYSQL_PDO) {
            foreach ($this->queries as $insert) {
                Database::insert($insert['query'], $insert['param']);
            }
        } else {
            //TODO: if recordset implements prepare
            if (count($this->query) > 0) {
                $rs = Database::getRecordset($this->dbConnection);
                foreach ($this->query as $query_index => $query) {
                    if ($rs->open($query->query)) {
                        foreach ($query->rows as $row) {
                            if (array_key_exists($row, $this->strippedClientData)) {
                                $rs->addnew();
                                if ($this->useCreateLog) {
                                    $datetime = $this->clientData->getSendDataTime();
                                    $datetime->setTimezone($this->dbTimezone);
                                    $rs->fields[$this->columnNameCreateBy] = $this->userId;
                                    $rs->fields[$this->columnNameCreateOn] = $datetime->format($this->columnFormatCreateOn);
                                }
                                if ($this->useArchiveLog) {
                                    $rs->fields[$this->columnNameArchive] = false;
                                }
                                foreach ($this->strippedClientData[$row] as $field => $value) {
                                    $rs->fields[$field] = $value->value;
                                }
                                $rs->update();
                            }
                        }
                        $rs->close();
                    }
                }
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    private function buildQuery(): bool
    {
        //TODO: if recordset implements prepare
        $result      = false;
        $this->query = [];
        global $dbDriver;
        if (count($this->strippedClientData) > 0) {
            foreach ($this->strippedClientData as $row => $stripped_client_data) {
                if (count($stripped_client_data) > 0) {
                    $columns = [];
                    foreach ($stripped_client_data as $column => $nil) {
                        $columns[] = $this->escapeCharacterPre.$column.$this->escapeCharacterPost;
                    }
                    if (count($columns) > 0) {
                        if ($this->useCreateLog === true) {
                            $columns[] = $this->escapeCharacterPre.$this->columnNameCreateBy.$this->escapeCharacterPost;
                            $columns[] = $this->escapeCharacterPre.$this->columnNameCreateOn.$this->escapeCharacterPost;
                        }
                        if ($this->useArchiveLog === true) {
                            $columns[] = $this->escapeCharacterPre.$this->columnNameArchive.$this->escapeCharacterPost;
                        }
                        foreach ($this->table as $table) {
                            if ($dbDriver === Environment::DRIVER_MYSQL_PDO) {
                                $param = [];
                                foreach ($columns as $column) {
                                    if (array_key_exists($row, $this->strippedClientData) && array_key_exists($column, $this->strippedClientData[$row])) {
                                        $param[$column] = $this->strippedClientData[$row][$column]->value;
                                    }
                                }
                                if (!empty($param)) {
                                    $this->queries[] = [
                                        'query' => 'INSERT INTO '.$table.' ('.implode(', ', array_keys($param)).') VALUES (:'.implode(', :', array_keys($param)).')',
                                        'param' => $param
                                    ];
                                }
                            } else {
                                $query = 'SELECT '.implode(', ', $columns).' FROM '.$table;
                                $found = false;
                                foreach ($this->query as $query_index => $query_object) {
                                    if (is_object($query_object) && isset($query_object->query) && $query_object->query === $query) {
                                        $found                                 = true;
                                        $this->query[$query_index]->rows[$row] = $row;
                                    }
                                }
                                if ($found === false) {
                                    $tmp             = new stdClass();
                                    $tmp->query      = $query;
                                    $tmp->rows[$row] = $row;
                                    $this->query[]   = $tmp;
                                }
                            }
                        }
                        $result = true;
                    }
                }
            }
        }
        return $result;
    }
}
