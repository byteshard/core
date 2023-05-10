<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Action;

use byteShard\Action\Cell\ShowCellHeader;

class ShowHeaderCell extends ShowCellHeader
{
    public function __construct(string ...$cells)
    {
        trigger_error('byteShard\Action\ShowHeaderCell is deprecated. Please update namespace to byteShard\Action\Cell\ShowCellHeader', E_USER_DEPRECATED);
        parent::__construct(...$cells);
    }
}