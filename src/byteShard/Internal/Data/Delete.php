<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Data;

use byteShard\Internal\Data;
use byteShard\Popup\Message;
use byteShard\Locale;
use byteShard\Enum;
use byteShard\Exception;

class Delete extends Data
{
    public function process(): array
    {
        $msg = new Message('');
        $msg->setLabel(Locale::get('byteShard.data.delete.failed.label'));
        if ($this->cell->getAccessType() !== Enum\AccessType::RW) {
            $msg->setMessage(Locale::get('byteShard.data.delete.failed.permission'));
            return $this->getErrorResult($msg->getNavigationArray());
        }
        if (empty($this->table)) {
            $msg->setMessage(Locale::get('byteShard.data.delete.failed.table_not_defined'));
            return $this->getErrorResult($msg->getNavigationArray());
        }
        if ($this->checkClientData($msg) === false) {
            return $this->getErrorResult($msg->getNavigationArray());
        }
        if ($this->checkReferences($msg) === false) {
            return $this->getErrorResult($msg->getNavigationArray());
        }
        if ($this->buildQuery() === false) {
            $msg->setMessage(Locale::get('byteShard.data.delete.failed.query'));
            return $this->getErrorResult($msg->getNavigationArray());
        }
        if ($this->connect($msg) === true) {
            if ($this->deleteData() === false) {
                $msg->setMessage(Locale::get('byteShard.data.delete.failed.delete'));
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
     * @return bool
     */
    private function buildQuery(): bool
    {
        $this->query = array();
        if (count($this->strippedClientData) > 0) {
            foreach ($this->strippedClientData as $row => $stripped_client_data) {
                $constraints = array();
                if (count($this->constraints) > 0) {
                    foreach ($this->constraints as $constraint) {
                        if ($constraint->value === null) {
                            if (array_key_exists($constraint->field, $stripped_client_data)) {
                                $escape_char = '';
                                if (Enum\DB\ColumnType::is_string($stripped_client_data[$constraint->field]->type)) {
                                    $escape_char = "'";
                                }
                                $constraints[] = $this->escapeCharacterPre.$constraint->field.$this->escapeCharacterPost.'='.$escape_char.$stripped_client_data[$constraint->field]->value.$escape_char;
                            } else {
                                throw new Exception(__METHOD__.': constraint not found in clientData');
                            }
                        } else {
                            //TODO: if column is varchar this will fail...
                            $constraints[] = $this->escapeCharacterPre.$constraint->field.$this->escapeCharacterPost.'='.$constraint->value;
                        }
                    }
                }
                if (count($constraints) > 0) {
                    foreach ($this->table as $table) {
                        $this->query[] = 'DELETE FROM '.$table.' WHERE '.implode(' AND ', $constraints);
                    }
                }
            }
        } elseif (count($this->constraints) > 0) {
            $constraints = array();
            foreach ($this->constraints as $constraint) {
                if ($constraint->value !== null) {
                    //TODO: if column is varchar this will fail...
                    $constraints[] = $this->escapeCharacterPre.$constraint->field.$this->escapeCharacterPost.'='.$constraint->value;
                } else {
                    throw new Exception(__METHOD__.': constraint not found in clientData');
                }
            }
            if (count($constraints) > 0) {
                foreach ($this->table as $table) {
                    $this->query[] = 'DELETE FROM '.$table.' WHERE '.implode(' AND ', $constraints);
                }
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    private function deleteData(): bool
    {
        if (count($this->query) > 0) {
            foreach ($this->query as $row => $query) {
                $this->dbConnection->execute($query);
            }
        }
        return true;
    }
}
