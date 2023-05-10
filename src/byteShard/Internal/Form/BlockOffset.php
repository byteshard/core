<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait BlockOffset
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait BlockOffset
{
    /**
     * left-side offset of the FormObject content (default 20)
     * @param int $offset
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setBlockOffset(int $offset): self
    {
        if (isset($this->attributes)) {
            $this->attributes['blockOffset'] = $offset;
        }
        return $this;
    }
}
