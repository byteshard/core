<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Event;

use byteShard\Internal\Action;

interface EventStorageInterface {
    /**
     * @internal
     * @param string $eventId
     * @param Event $event
     */
    public function setEventForInteractiveObject(string $eventId, Event $event);

    /**
     * returns an empty array or an array of Action objects
     * @internal
     * @param string $eventId
     * @return Action[]
     */
    public function getActionsForEvent(string $eventId): array;
}
