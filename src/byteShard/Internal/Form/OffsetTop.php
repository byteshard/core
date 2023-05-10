<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait OffsetTop
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait OffsetTop
{
    /**
     * sets the top relative offset of the FormObject (both FormObject and Label)
     * @param int $offsetTop
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setOffsetTop(int $offsetTop): self
    {
        if (isset($this->attributes)) {
            $this->attributes['offsetTop'] = $offsetTop;
        }
        return $this;
    }
}
