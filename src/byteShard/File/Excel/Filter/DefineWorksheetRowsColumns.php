<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File\Excel\Filter;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Class DefineWorksheetRowsColumns
 * @package byteShard\File\Excel\Filter
 */
class DefineWorksheetRowsColumns implements IReadFilter
{
    private array $worksheetNames;
    private int   $rowStart;
    private int   $rowEnd;
    private array $columnsArray;

    public function __construct(array $worksheetNames, int $rowStart, int $rowEnd, array $columnsArray)
    {
        $this->worksheetNames = $worksheetNames;
        $this->rowStart       = $rowStart;
        $this->rowEnd         = $rowEnd;
        $this->columnsArray   = $columnsArray;
    }

    public function readCell($column, $row, $worksheetName = ''): bool
    {
        if (isset($this->worksheetNames[$worksheetName])) {
            if ($row >= $this->rowStart && $row <= $this->rowEnd) {
                if (isset($this->columnsArray[$column])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
