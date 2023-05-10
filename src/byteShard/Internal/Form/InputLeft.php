<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Form\Enum;

/**
 * Trait InputLeft
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait InputLeft
{
    /**
     * sets the left absolute offset of the FormObject.
     * the position is set to "absolute"
     * @param int $inputLeft
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setInputLeft(int $inputLeft): self
    {
        if (isset($this->attributes)) {
            $this->attributes['inputLeft'] = $inputLeft;
            $this->attributes['position']  = Enum\Label\Position::ABSOLUTE->value;
        }
        return $this;
    }
}
