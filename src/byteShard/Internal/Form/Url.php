<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Url
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Url
{
    /**
     * the path to the server-side script (relative to the index file) which will parse the requests received from the uploader. Used in the html5/html4 modes.
     * @param string $url
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setUrl(string $url): self
    {
        if (isset($this->attributes)) {
            $this->attributes['url'] = $url;
        }
        return $this;
    }
}
