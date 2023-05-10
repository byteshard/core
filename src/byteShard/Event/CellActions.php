<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Event;

use byteShard\Internal\Action;

class CellActions implements EventActionInterface
{
    private array $actions;

    public function __construct(Action ...$actions)
    {
        $this->actions = $actions;
    }

    public function getActions(?string $objectId): array
    {
        return $this->actions;
    }
}
