<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Form\Enum;

/**
 * Trait MinutesInterval
 * @package byteShard\Internal\Form
 * @property array $attributes
 */
trait MinutesInterval
{
    /**
     * the time interval (in minutes) for the predefined values in the time selector. The default value is 5
     * @param Enum\Calendar\Minutes $minutesInterval
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setMinutesInterval(Enum\Calendar\Minutes $minutesInterval): self
    {
        if (isset($this->attributes)) {
            $this->attributes['minutesInterval'] = $minutesInterval->value;
        }
        return $this;
    }
}
