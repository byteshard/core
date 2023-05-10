<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait OffsetLeft
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait OffsetLeft
{
    /**
     * sets the left relative offset of the FormObject (both FormObject and Label)
     * @param int $offsetLeft
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setOffsetLeft(int $offsetLeft): self
    {
        if (isset($this->attributes)) {
            $this->attributes['offsetLeft'] = $offsetLeft;
        }
        return $this;
    }
}
