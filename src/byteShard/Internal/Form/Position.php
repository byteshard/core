<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Form\Enum;

/**
 * Trait Position
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Position
{
    /**
     * defines the position of the label relative to the FormObject
     * @param Enum\Label\Position $labelPosition
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setPosition(Enum\Label\Position $labelPosition): self {
        if (isset($this->attributes)) {
            $this->attributes['position'] = $labelPosition->value;
        }
        return $this;
    }
}
