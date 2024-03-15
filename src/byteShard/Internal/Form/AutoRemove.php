<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait AutoRemove
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait AutoRemove
{
    /**
     * defines whether files will be removed from the upload object after complete uploading. The default value - false
     * @param bool $bool
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setAutoRemove(bool $bool = true): self
    {
        if (isset($this->attributes)) {
            $this->attributes['autoRemove'] = $bool;
        }
        return $this;
    }
}
