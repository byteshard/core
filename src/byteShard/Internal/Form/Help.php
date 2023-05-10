<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Help
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Help
{
    /**
     * opens a popup next to the form object. The contents can
     * @param boolean $bool = true [optional]
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setHelp(bool $bool = true): self
    {
        if (isset($this->help)) {
            $this->help = $bool;
        }
        return $this;
    }
}
