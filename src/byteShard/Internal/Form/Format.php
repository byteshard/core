<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Format
 * @package byteShard\Form\Internal
 */
trait Format
{
    /**
     * allows setting individual look for a specific FormObjects instance (see details below)
     * @param string $format
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setFormat(string $format): self
    {
        if (isset($this->attributes)) {
            $this->attributes['format'] = $format;
        }
        return $this;
    }
}
