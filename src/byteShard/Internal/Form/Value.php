<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Value
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Value
{
    /**
     * the initial value of the Form Object
     * @param null|string|int|float $stringOrInt
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setValue(null|string|int|float $stringOrInt): self
    {
        if (isset($this->attributes)) {
            $this->attributes['value'] = $stringOrInt;
        }
        return $this;
    }

    public function getValue(): null|string|int|float
    {
        return $this->attributes['value'] ?? null;
    }
}
