<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Tab\Event;
use byteShard\Internal\Event\TabEvent;

/**
 * Class OnClose
 * @package byteShard\Tab\Event
 */
class OnClose extends TabEvent
{
    protected static string $contentEventName = 'onTabClose';
    protected static string $event = 'onClose';
}
