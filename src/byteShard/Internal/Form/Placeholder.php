<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Placeholder
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Placeholder
{
    /**
     * the placeholder value of the Form Object
     * @param string $string
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setPlaceholder(?string $string = 'bs_locale'): self
    {
        if (property_exists($this, 'placeholder')) {
            $this->placeholder = $string;
        }
        return $this;
    }
}
