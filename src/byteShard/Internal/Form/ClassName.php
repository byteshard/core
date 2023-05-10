<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait ClassName
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait ClassName
{
    /**
     * the user-defined css class for the Form Object.
     * @param string $className
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setClassName(string $className): self
    {
        if (isset($this->attributes)) {
            $this->attributes['className'] = $className;
        }
        return $this;
    }
}
