<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait InputWidth
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait InputWidth
{
    /**
     * the width of the FormObject. The default value is 'auto'.
     * @param int|null $width
     * @return static
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setInputWidth(?int $width): static
    {
        if (isset($this->attributes)) {
            $this->attributes['inputWidth'] = $width ?? 'auto';
        }
        return $this;
    }

    /**
     * @return string|null
     * @API
     */
    public function getInputWidth(): ?string
    {
        if (isset($this->attributes) && array_key_exists('inputWidth', $this->attributes)) {
            return (string)$this->attributes['inputWidth'];
        }
        return null;
    }
}
