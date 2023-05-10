<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File\Excel\Filter;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Class DefineWorksheetColumns
 * @package byteShard\File\Excel\Filter
 */
class DefineWorksheetColumns implements IReadFilter
{
    private array $worksheetNames;
    private array $columnsArray;

    public function __construct(array $worksheetNames, array $columnsArray)
    {
        $this->worksheetNames = $worksheetNames;
        $this->columnsArray   = $columnsArray;
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
