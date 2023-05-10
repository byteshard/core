<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Action;

use byteShard\ID\ID;

class ActionResultMigrationHelper implements ActionResultInterface
{
    public function __construct(private readonly array $oldResultStyle) {
        
    }
    
    public function getResultArray(?ID $containerId): array
    {
        return $this->oldResultStyle;
    }
}
