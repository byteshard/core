<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Action;

use byteShard\Internal\Action;
use byteShard\Internal\Action\ActionResultInterface;

/**
 * Class SelectTab
 * @package byteShard\Action
 */
class SelectTab extends Action
{
    protected function runAction(): ActionResultInterface
    {
        return new Action\ActionResult();
    }
    // $action['tabBar']['selectTab'][]['ID'] = $this->tabIDToSelect;
    // TODO: check of tab parent Tab needs to be selected in case I want to select a child Tab
}
