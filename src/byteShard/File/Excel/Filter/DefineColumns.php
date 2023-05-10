<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File\Excel\Filter;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Class DefineColumns
 * @package byteShard\File\Excel\Filter
 */
class DefineColumns implements IReadFilter
{
    private array $columnsArray;

    public function __construct(array $columnsArray)
    {
        $this->columnsArray = $columnsArray;
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        if (isset($this->columnsArray[$columnAddress])) {
            return true;
        } else {
            return false;
        }
    }
}
