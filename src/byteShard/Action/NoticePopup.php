<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Action;

use byteShard\Internal\Action\Popup;
use byteShard\Popup\Enum\Message\Type;

class NoticePopup extends Popup
{
    protected Type   $type         = Type::NOTICE;
    protected string $localeSuffix = 'Notice';
}
