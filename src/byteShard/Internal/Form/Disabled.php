<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Disabled
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Disabled
{
    /**
     * disables/enables the item.
     * @param boolean $bool = true [optional]
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setDisabled(bool $bool = true): self
    {
        if (isset($this->attributes)) {
            if ($bool === true) {
                $this->attributes['disabled'] = true;
            } elseif (isset($this->attributes['disabled'])) {
                unset($this->attributes['disabled']);
            }
        }
        return $this;
    }
}
