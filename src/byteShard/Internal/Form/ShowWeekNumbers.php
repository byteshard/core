<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait ShowWeekNumbers
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait ShowWeekNumbers
{
    /**
     * enables/disables an additional left column with weeks' numbers. By default, false
     * @param boolean $bool = true [optional]
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setShowWeekNumbers(bool $bool = true): self
    {
        if (isset($this->attributes)) {
            if ($bool === true) {
                $this->attributes['showWeekNumbers'] = true;
            } elseif (isset($this->attributes['showWeekNumbers'])) {
                unset($this->attributes['showWeekNumbers']);
            }
        }
        return $this;
    }
}
