<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait SlUrl
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait SlUrl
{
    /**
     * @param string $url
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setSilverlightUrl(string $url): self
    {
        if (isset($this->attributes)) {
            $this->attributes['slUrl'] = $url;
        }
        return $this;
    }
}
