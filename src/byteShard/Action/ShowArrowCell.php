<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Action;

use byteShard\Action\Cell\ShowCellArrow;

class ShowArrowCell extends ShowCellArrow
{
    public function __construct(string ...$cells)
    {
        trigger_error('byteShard\Action\ShowArrowCell is deprecated. Please update namespace to byteShard\Action\Cell\ShowCellArrow', E_USER_DEPRECATED);
        parent::__construct(...$cells);
    }
}