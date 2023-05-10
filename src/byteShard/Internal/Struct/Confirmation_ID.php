<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Struct;

/**
 * Class Popup_ID
 * @package byteShard\Internal\Struct
 */
class Confirmation_ID extends Popup_ID
{
    public string $Confirmation_ID;

    public function __toString()
    {
        return $this->Confirmation_ID;
    }
}
