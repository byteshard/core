<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait SlPath
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait SlPath
{
    /**
     * @param $path
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setSilverlightPath(string $path): self
    {
        if (isset($this->attributes)) {
            $this->attributes['slXap'] = $path;
        }
        return $this;
    }
}
