<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Database;

interface RecordsetInterface
{
    public function open(string $query): bool;

    public function close(): void;

    public function delete(): void;

    public function addnew(): void;

    public function update(): ?int;

    public function recordcount(): int;

    public function movenext(): void;
}