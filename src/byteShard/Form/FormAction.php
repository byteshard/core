<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form;

use byteShard\Event\CellActions;
use byteShard\Event\EventActionInterface;
use byteShard\Internal\Action;

class FormAction implements EventActionInterface
{
    private array $actions;

    public function __construct(private readonly string $objectId, Action ...$actions)
    {
        trigger_error(self::class.' is deprecated. Please use '.CellActions::class.' instead', E_USER_DEPRECATED);
        $this->actions = $actions;
    }

    public function getActions(?string $objectId): array
    {
        if ($this->objectId === $objectId) {
            return $this->actions;
        }
        return [];
    }
}