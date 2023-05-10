<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait LabelWidth
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait LabelWidth
{
    /**
     * the width of DIV where the label is placed (not the font size). The default value is 'auto'.
     * @param int|null $int
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setLabelWidth(?int $int = null): self
    {
        if (isset($this->attributes)) {
            $this->attributes['labelWidth'] = $int ?? 'auto';
        }
        return $this;
    }
}
