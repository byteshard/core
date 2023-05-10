<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Event;
use byteShard\Internal\Event;

/**
 * Class OnButtonClick
 * @package byteShard\Form\Event
 */
class OnBlur extends Event\FormEvent
{
    protected static string $event = 'onBlur';
}
