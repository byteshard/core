<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File\Excel\Filter;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Class DefineRowsColumns
 * @package byteShard\File\Excel\Filter
 */
class DefineRowsColumns implements IReadFilter
{
    private int   $rowStart;
    private int   $rowEnd;
    private array $columnsArray;

    public function __construct(int $rowStart, int $rowEnd, array $columnsArray)
    {
        $this->rowStart     = $rowStart;
        $this->rowEnd       = $rowEnd;
        $this->columnsArray = $columnsArray;
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        if ($row >= $this->rowStart && $row <= $this->rowEnd) {
            if (isset($this->columnsArray[$columnAddress])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
