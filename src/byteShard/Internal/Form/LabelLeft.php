<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Form\Enum;

/**
 * Trait LabelLeft
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait LabelLeft
{
    /**
     * sets the left absolute offset of label.
     * the position is set to "absolute"
     * @param int $labelLeft
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setLabelLeft(int $labelLeft): self
    {
        if (isset($this->attributes)) {
            $this->attributes['labelLeft'] = $labelLeft;
            $this->attributes['position']  = Enum\Label\Position::ABSOLUTE->value;
        }
        return $this;
    }
}
