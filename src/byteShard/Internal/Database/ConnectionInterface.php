<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Database;

use byteShard\Database\Struct\Parameters;

interface ConnectionInterface
{
    public function setParameters(Parameters $parameters): self;

    public function setServer(string $server): self;

    public function setPort(int $port): self;

    public function setDB(string $db): self;

    public function execute(string $query): bool;

    /** close the current database connection */
    public function disconnect(): void;
}
