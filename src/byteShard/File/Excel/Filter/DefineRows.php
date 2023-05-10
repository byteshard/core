<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File\Excel\Filter;


use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Class DefineRows
 * @package byteShard\File\Excel\Filter
 */
class DefineRows implements IReadFilter
{
    private int $rowStart;
    private int $rowEnd;

    public function __construct(int $rowStart, int $rowEnd)
    {
        $this->rowStart = $rowStart;
        $this->rowEnd   = $rowEnd;
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        if ($row >= $this->rowStart && $row <= $this->rowEnd) {
            return true;
        } else {
            return false;
        }
    }
}
