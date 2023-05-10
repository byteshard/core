<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Event;

use byteShard\Internal\Action;

class RadioActions extends ObjectValueActions
{

    public function __construct(private readonly string $objectId, Action ...$actions)
    {
        trigger_error('Class RadioActions is deprecated. Use ObjectValueActions instead.', E_USER_DEPRECATED);
        parent::__construct($this->objectId, ...$actions);
    }
}