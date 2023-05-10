<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Event;
use byteShard\Internal\Event;

/**
 * Class OnPoll
 * @package byteShard\Form\Event
 */
class OnPoll extends Event\FormEvent
{
    protected static string $event = 'onPoll';
}
