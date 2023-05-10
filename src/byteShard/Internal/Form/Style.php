<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Style
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Style
{
    /**
     * specifies css element.style that will be applied to the FormObject
     * @param string $style
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setStyle(string $style): self
    {
        if (isset($this->attributes)) {
            $this->attributes['style'] = $style;
        }
        return $this;
    }
}
