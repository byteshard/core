<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait SwfUrl
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait SwfUrl
{
    /**
     * @param string $url
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setSwfUrl(string $url): self
    {
        if (isset($this->attributes)) {
            $this->attributes['swfUrl'] = $url;
        }
        return $this;
    }
}
