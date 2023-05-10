<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait ServerDateFormat
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait ServerDateFormat
{
    /**
     * the format in which the date is stored on server
     * @param string $dateFormat
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setServerDateFormat(string $dateFormat): self
    {
        if (isset($this->attributes)) {
            $this->attributes['serverDateFormat'] = $dateFormat;
        }
        return $this;
    }
}
