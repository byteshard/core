<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Filtering
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Filtering
{
    /**
     * The whole list is loaded on the client side, and as a user begins to type, the list is updated with the appropriate values (which contain or begin from the characters typed).
     * @param bool $bool
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setFiltering(bool $bool = true): self
    {
        if ($bool === true) {
            $this->attributes['filtering'] = true;
        } elseif (isset($this->attributes['filtering'])) {
            unset($this->attributes['filtering']);
        }
        return $this;
    }
}
