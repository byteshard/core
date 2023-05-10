<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait Userdata
 * @package byteShard\Form\Internal
 */
trait Userdata
{
    /**
     * sets some user data for the FormObject (key:value pairs)
     * @param array $keyValueArray
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setUserdata(array $keyValueArray): self
    {
        foreach ($keyValueArray as $key => $value) {
            $this->userdata[$key] = $value;
        }
        return $this;
    }
}
