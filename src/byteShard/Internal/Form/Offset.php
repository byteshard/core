<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Offset
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Offset
{
    /**
     * sets the distance between columns
     * @param int $offset
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setOffset(int $offset): self
    {
        if (isset($this->attributes)) {
            $this->attributes['offset'] = $offset;
        }
        return $this;
    }
}
