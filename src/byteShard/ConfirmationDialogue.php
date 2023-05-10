<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard;

use byteShard\Action\ConfirmAction;

interface ConfirmationDialogue
{
    function defineConfirmationDialogue(ConfirmAction $confirmation);
}
