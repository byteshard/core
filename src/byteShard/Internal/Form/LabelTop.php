<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Form\Enum;

/**
 * Trait LabelTop
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait LabelTop
{
    /**
     * sets the top absolute offset of label.
     * the position is set to "absolute"
     * @param int $labelTop
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setLabelTop(int $labelTop): self {
        if (isset($this->attributes)) {
            $this->attributes['labelTop'] = $labelTop;
            $this->attributes['position'] = Enum\Label\Position::ABSOLUTE->value;
        }
        return $this;
    }
}
