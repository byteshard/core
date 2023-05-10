<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Scheduler\Event;

use byteShard\Internal\Event\SchedulerEvent;

class OnEmptyClick extends SchedulerEvent
{
    protected static string $event = 'onEmptyClick';
}
