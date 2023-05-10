<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait AutoStart
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait AutoStart
{
    /**
     * Whether to auto start the upload after selecting a file or not
     * @param bool $bool
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setAutoStart(bool $bool = true): self
    {
        if (isset($this->attributes)) {
            $this->attributes['autoStart'] = $bool;
        }
        return $this;
    }
}
