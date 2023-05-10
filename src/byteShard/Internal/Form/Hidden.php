<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Hidden
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Hidden
{
    /**
     * hides/shows the item. The default value - false (the item is shown).
     * @param boolean $bool = true [optional]
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setHidden(bool $bool = true): self
    {
        if (isset($this->attributes)) {
            if ($bool === true) {
                $this->attributes['hidden'] = true;
            } elseif (isset($this->attributes['hidden'])) {
                unset($this->attributes['hidden']);
            }
        }
        return $this;
    }
}
