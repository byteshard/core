<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File\Excel\Filter;


use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Class DefineWorksheet
 * @package byteShard\File\Excel\Filter
 */
class DefineWorksheet implements IReadFilter
{
    private array $worksheetNames;

    public function __construct(array $worksheetNames)
    {
        $this->worksheetNames = $worksheetNames;
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        if (isset($this->worksheetNames[$worksheetName])) {
            return true;
        } else {
            return false;
        }
    }
}
