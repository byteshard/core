<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Enum\Cast;

/**
 * Trait EnableTime
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait EnableTime
{
    /**
     * defines whether in the bottom of calendar, time manage elements will be presented
     * @param boolean $bool = true [optional]
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setEnableTime(bool $bool = true): self
    {
        if (isset($this->attributes)) {
            if ($bool === true) {
                $this->attributes['enableTime'] = true;
                self::$cast                     = Cast::DATETIME;
            } elseif (isset($this->attributes['enableTime'])) {
                unset($this->attributes['enableTime']);
                self::$cast = Cast::DATE;
            }
        }
        return $this;
    }
}
