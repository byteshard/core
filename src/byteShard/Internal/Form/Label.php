<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Class Label
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Label
{
    /**
     * the text label of the FormObject.
     * @param string $label
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setLabel(string $label): self
    {
        if (isset($this->attributes)) {
            $this->attributes['label'] = $label;
        }
        return $this;
    }
}
