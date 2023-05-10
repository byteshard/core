<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Action;

use byteShard\Cell;
use byteShard\Internal\Action;
use byteShard\Internal\Action\ActionResultInterface;
use byteShard\Internal\CellContent;
use Exception;

/**
 * Class FilterGrid
 * @package byteShard\Action
 */
class PollCellMethod extends Action
{
    private string $cellId = '';

    /**
     * PollCellMethod constructor.
     * @param Cell|CellContent $cell
     * @throws Exception
     */
    public function __construct(Cell|CellContent $cell)
    {
        parent::__construct();
        if ($cell instanceof CellContent) {
            $cell = $cell->getCell();
        }
    }

    protected function runAction(): ActionResultInterface
    {
        return new Action\ActionResult();
    }
}
