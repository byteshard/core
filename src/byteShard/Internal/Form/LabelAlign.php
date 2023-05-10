<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Form\Enum;

/**
 * Trait LabelAlign
 * @package byteShard\Internal\Form
 * @property array $attributes
 */
trait LabelAlign
{
    /**
     * the alignment of the Label within the defined width.
     * @param Enum\Label\Align $align
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setLabelAlign(Enum\Label\Align $align): self
    {
        if (isset($this->attributes)) {
            $this->attributes['labelAlign'] = $align->value;
        }
        return $this;
    }
}
