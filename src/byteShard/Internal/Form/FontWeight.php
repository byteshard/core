<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait FontWeight
 * @package byteShard\Form\Internal
 * @property string $fontWeight
 */
trait FontWeight
{
    /**
     * allows setting of the font weight
     * @param string $fontWeight
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setFontWeight(string $fontWeight): self
    {
        if (isset($this->fontWeight)) {
            $this->fontWeight = $fontWeight;
        }
        return $this;
    }
}
