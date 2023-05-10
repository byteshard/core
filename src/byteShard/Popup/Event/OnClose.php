<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Popup\Event;
use byteShard\Internal\Event\PopupEvent;

/**
 * Class BSPopupEvent_onClose
 */
class OnClose extends PopupEvent
{
    protected static string $contentEventName = 'onPopupClose';
    protected static string $event = 'onClose';
}
