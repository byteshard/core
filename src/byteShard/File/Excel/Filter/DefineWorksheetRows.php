<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File\Excel\Filter;


use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Class DefineWorksheetRows
 * @package byteShard\File\Excel\Filter
 */
class DefineWorksheetRows implements IReadFilter
{
    private array $worksheetNames;
    private int   $rowStart;
    private int   $rowEnd;

    public function __construct(array $worksheetNames, int $rowStart, int $rowEnd)
    {
        $this->worksheetNames = $worksheetNames;
        $this->rowStart       = $rowStart;
        $this->rowEnd         = $rowEnd;
    }

    /**
     * @param $columnAddress
     * @param $row
     * @param $worksheetName
     * @return bool
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection PhpMissingParamTypeInspection
     */
    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        if (isset($this->worksheetNames[$worksheetName])) {
            if ($row >= $this->rowStart && $row <= $this->rowEnd) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
