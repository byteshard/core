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
class Popup_ID extends Navigation_ID
{
    public string $Tab_ID;
    public string $Popup_ID = '';

    public function __toString()
    {
        return $this->Popup_ID;
    }
}
