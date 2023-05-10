<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;

use byteShard\Internal\Action;

class ButtonGroupClosePopupStyled extends ButtonGroupClosePopup
{
    public function __construct(string $id = '', Action ...$approveActions)
    {
        parent::__construct($id, ...$approveActions);
        $this->setStyled();
    }
}
