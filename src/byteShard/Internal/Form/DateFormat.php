<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait DateFormat
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait DateFormat
{
    /**
     * sets format of date presentation in input
     * @param string $format
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setDateFormat(string $format): self
    {
        if (isset($this->attributes)) {
            $this->attributes['dateFormat'] = $format;
        }
        return $this;
    }
}
