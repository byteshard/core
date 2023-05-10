<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Popup\Enum\Message;

/**
 * Class Type
 * @package byteShard\Popup\Enum\Message
 */
enum Type: string
{
    case ERROR = 'message_error';
    case WARNING = 'message_warning';
    case NOTICE = 'message_notice';
}
