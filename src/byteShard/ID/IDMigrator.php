<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\ID;

class IDMigrator
{
    public string $LCell_ID;
    private string $containerId;
    public function __construct(string $containerId, string $cellId)
    {
        $this->LCell_ID = $cellId;
        $this->containerId = $containerId;
    }

    public function __toString(): string
    {
        return $this->containerId;
    }
}