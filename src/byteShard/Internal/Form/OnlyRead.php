<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Enum\AccessType;

/**
 * Trait OnlyRead
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait OnlyRead
{
    /**
     * specifies whether the FormObjects value can be changed by button click in browser (meanwhile, the FormObjects value can be changed programmatically anytime)
     * @param boolean $bool = true [optional]
     * @return static
     * @API
     */
    public function setReadonly(bool $bool = true): static
    {
        if (isset($this->attributes)) {
            if ($bool === true) {
                $this->attributes['readonly'] = true;
                $this->setAccessType(AccessType::READ);
            } elseif (isset($this->attributes['readonly'])) {
                unset($this->attributes['readonly']);
            }
        }
        return $this;
    }
}
