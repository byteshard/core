<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Tab\Event;
use byteShard\Internal\Event\TabEvent;

/**
 * Class OnOpen
 * @package byteShard\Tab\Event
 */
class OnOpen extends TabEvent
{
    protected static string $contentEventName = 'onTabOpen';
    protected static string $event = 'onOpen';
}
