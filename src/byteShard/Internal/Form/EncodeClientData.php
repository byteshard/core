<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait EncodeClientData
 * @package byteShard\Form\Internal
 * @property array $parameters
 */
trait EncodeClientData
{
    /**
     * use base64 encoding to transfer client data to server. This will skip any sanitation and opens possible security issues like xss.
     * the intended use is to transfer passcodes and similar data which must not be altered in any way
     * @API
     */
    public function encodeClientData(bool $bool = true): self
    {
        if (isset($this->parameters)) {
            if ($bool === true) {
                $this->parameters['afterDataLoading']['base64'] = true;
            } elseif (isset($this->attributes['required'])) {
                unset($this->parameters['afterDataLoading']['base64']);
            }
        }
        return $this;
    }
}
