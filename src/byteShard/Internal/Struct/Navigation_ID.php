<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Struct;

/**
 * Class Navigation_ID
 * @package byteShard\Internal\Struct
 */
class Navigation_ID extends ID
{
    public array $Navigation = [];

    public function __toString()
    {
        return match (get_class($this)) {
            'Tab_ID' => $this->Tab_ID,
            ''       => $this->Popup_ID,
            default  => $this->ID,
        };
    }

    /**
     * @return string
     */
    public function getCellID(): string
    {
        if (property_exists($this, 'LCell_ID')) {
            return $this->LCell_ID;
        }
        return '';
    }
}
