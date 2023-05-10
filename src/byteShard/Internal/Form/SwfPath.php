<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait SwfPath
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait SwfPath
{
    /**
     * @param string $path
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setSwfPath(string $path): self
    {
        if (isset($this->attributes)) {
            $this->attributes['swfPath'] = $path;
            //TODO: own setters for autoStart and autoRemove
            $this->attributes['autoStart']  = true;
            $this->attributes['autoRemove'] = true;
        }
        return $this;
    }
}
