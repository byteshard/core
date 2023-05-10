<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Info
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Info
{
    /**
     * adds the "[?]" icon after the FormObject (related event - onInfo).
     * @param boolean $bool = true [optional]
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setInfo(bool $bool = true): self
    {
        if (isset($this->attributes)) {
            if ($bool === true) {
                $this->attributes['info'] = true;
            } elseif (isset($this->attributes['info'])) {
                unset($this->attributes['info']);
            }
        }
        return $this;
    }
}
