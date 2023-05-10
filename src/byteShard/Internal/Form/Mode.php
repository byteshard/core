<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Form\Enum;

/**
 * Trait Mode
 * @package byteShard\Internal\Form
 * @property array $attributes
 */
trait Mode
{
    /**
     * allows you to manually set the mode in use. Beware, once you set the mode manually, the control will work in this mode permanently independently on the browser you use
     * @param Enum\UploadMode $mode
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setMode(Enum\UploadMode $mode): self
    {
        if (isset($this->attributes)) {
            $this->attributes['mode'] = $mode->value;
        }
        return $this;
    }
}
