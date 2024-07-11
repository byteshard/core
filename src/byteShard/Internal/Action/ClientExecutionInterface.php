<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Action;

use byteShard\ID\ID;

interface ClientExecutionInterface
{
    public function getClientExecution(): bool;
    public function setClientExecution(bool $bool = true): self;
    public function getClientExecutionMethod(): string;

    /**
     * @param ID $containerId
     * @return array<string> form object IDs
     */
    public function getClientExecutionItems(ID $containerId): array;
}
