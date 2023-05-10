<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Form\Enum;

/**
 * Trait InputTop
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait InputTop
{
    /**
     * sets the top absolute offset of the FormObject.
     * the position is set to "absolute"
     * @param int $inputTop
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setInputTop(int $inputTop): self
    {
        if (isset($this->attributes)) {
            $this->attributes['inputTop'] = $inputTop;
            $this->attributes['position'] = Enum\Label\Position::ABSOLUTE->value;
        }
        return $this;
    }
}
