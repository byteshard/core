<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait MaxLength
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait MaxLength
{
    /**
     * the max number of characters that can be entered in the FormObject
     * @param int $maxLength
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setMaxLength(int $maxLength): self
    {
        if (isset($this->attributes)) {
            $this->attributes['maxLength'] = $maxLength;
        }
        return $this;
    }
}
