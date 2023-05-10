<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait WeekStart
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait WeekStart
{
    /**
     * sets the start day of a week. '1' relates to Monday and '7' to Sunday.
     * @param \byteShard\Form\Enum\Calendar\WeekStart $weekStart
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setWeekStart(\byteShard\Form\Enum\Calendar\WeekStart $weekStart): self
    {
        if (isset($this->attributes)) {
            $this->attributes['weekStart'] = $weekStart->value;
        }
        return $this;
    }
}
