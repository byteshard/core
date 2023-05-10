<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;

use byteShard\Internal\Action;

class ButtonGroupStyled extends ButtonGroup
{
    public function __construct(string $id = '', ?Action $cancelAction = null, Action ...$approveActions)
    {
        parent::__construct($id, $cancelAction, ...$approveActions);
        $this->setStyled();
    }
}
