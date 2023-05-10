<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait LabelHeight
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait LabelHeight
{
    /**
     * the height of DIV where the label is placed (not the font size). The default value is 'auto'.
     * @param int|null $height
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setLabelHeight(?int $height): self
    {
        if (isset($this->attributes)) {
            $this->attributes['labelHeight'] = $height ?? 'auto';
        }
        return $this;
    }
}
