<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait InputHeight
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait InputHeight
{
    /**
     * the height of the FormObject. The default value is 'auto'.
     * @param int|null $height
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setInputHeight(?int $height): self
    {
        if (isset($this->attributes)) {
            $this->attributes['inputHeight'] = $height ?? 'auto';
        }
        return $this;
    }
}
