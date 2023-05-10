<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Form\Enum;

/**
 * Trait CalendarPosition
 * @package byteShard\Internal\Form
 * @property array $attributes
 */
trait CalendarPosition
{
    /**
     * sets the position pop-up calendar will appear from. The default value is bottom
     * @param Enum\Calendar\Position $position
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setCalendarPosition(Enum\Calendar\Position $position): self
    {
        if (isset($this->attributes)) {
            $this->attributes['calendarPosition'] = $position->value;
        }
        return $this;
    }
}
