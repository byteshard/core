<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Event;
use byteShard\Internal\Event;

/**
 * Class OnInputChange
 * @package byteShard\Form\Event
 */
class OnInputChange extends Event\FormEvent
{
    protected static string $event = 'onInputChange';
    private bool            $allFormObjects = false;

    public function setGetAllFormObjects(): self
    {
        $this->allFormObjects = true;
        return $this;
    }

    public function getGetAllFormObjects(): bool
    {
        return $this->allFormObjects;
    }
}
