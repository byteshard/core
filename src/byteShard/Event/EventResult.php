<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Event;

use byteShard\Internal\Action;

class EventResult
{
    /** @var EventActionInterface[] */
    private array $resultActions;

    public function __construct(EventActionInterface ...$resultActions)
    {
        $this->resultActions = $resultActions;
    }

    /**
     * @param string $objectId
     * @param string $objectValue
     * @return Action[]
     */
    public function getResultActions(string $objectId, string $objectValue): array
    {
        $result = [];
        foreach ($this->resultActions as $resultAction) {
            if ($resultAction instanceof ObjectValueActions) {
                $result = array_merge($result, $resultAction->getActions($objectValue));
            } else {
                $result = array_merge($result, $resultAction->getActions($objectId));
            }
        }
        return $result;
    }
}
