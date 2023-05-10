<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Checked
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Checked
{
    /**
     * defines whether the FormObject will be checked initially. The default value is false
     * @param bool $bool = true [optional]
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setChecked(bool $bool = true): self
    {
        if (isset($this->attributes)) {
            if ($bool === true) {
                $this->attributes['checked'] = true;
            } elseif (isset($this->attributes['checked'])) {
                unset($this->attributes['checked']);
            }
        }
        return $this;
    }
}
