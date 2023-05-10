<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Required
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait Required
{
    /**
     * adds the * icon after the FormObjects Label marking the FormObject as mandatory. Also, setting the attribute to true automatically assigns the 'NotEmpty' validation rule to the input
     * @param bool $bool
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setRequired(bool $bool = true): self
    {
        if (isset($this->attributes)) {
            if ($bool === true) {
                $this->attributes['required'] = true;
            } elseif (isset($this->attributes['required'])) {
                unset($this->attributes['required']);
            }
        }
        return $this;
    }
}
