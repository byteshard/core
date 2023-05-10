<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form;

use byteShard\Event\EventActionInterface;
use byteShard\Event\ObjectActions;
use byteShard\Internal\Action;

class ControlAction implements EventActionInterface
{
    private array $actions;
    private string $objectId;
    public function __construct(string $objectId, Action ...$actions) {
        trigger_error(self::class.'is deprecated. Please use '.ObjectActions::class.' instead', E_USER_DEPRECATED);
        $this->objectId = $objectId;
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