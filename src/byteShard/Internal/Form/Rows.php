<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Rows
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Rows
{
    /**
     * used to present textarea (instead of a single input) of the specified height
     * @param int $rows
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setRows(int $rows): self
    {
        if (isset($this->attributes)) {
            $this->attributes['rows'] = $rows;
        }
        return $this;
    }
}
