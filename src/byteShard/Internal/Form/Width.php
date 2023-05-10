<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Width
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Width
{
    /**
     * the width of the FormObject
     * @param int $width
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setWidth(int $width): self
    {
        if (isset($this->attributes)) {
            $this->attributes['width'] = $width;
        }
        return $this;
    }
}
