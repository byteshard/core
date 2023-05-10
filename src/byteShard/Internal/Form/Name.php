<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Name
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Name
{
    /**
     * the identification name. Used for referring to the FormObject
     * is set in the FormObject constructor
     * @param string $string
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setName(string $string): self
    {
        if (isset($this->attributes)) {
            $this->attributes['name'] = $string;
        }
        return $this;
    }
}
