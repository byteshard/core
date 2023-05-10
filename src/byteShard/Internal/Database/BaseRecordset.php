<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Database;

/**
 * Class BaseRecordset
 * @package byteShard\Internal\Database
 */
abstract class BaseRecordset implements RecordsetInterface
{
    public array    $fields = [];
    public bool     $EOF    = true;
    public bool     $BOF    = true;
    public string   $query;
    public mixed    $recordset;
    protected mixed $connection;

    /**
     * @param string $query
     */
    abstract public function open(string $query): bool;

    abstract public function close(): void;

    abstract public function delete(): void;

    abstract public function addnew(): void;

    abstract public function update(): ?int;

    abstract public function recordcount(): int;

    abstract public function movenext(): void;
}
