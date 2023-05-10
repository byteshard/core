<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File\Excel;

use byteShard\Internal\File\ExcelBase;
use byteShard\File\Excel\Filter;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Class Worksheet
 * @package byteShard\File\Excel
 */
class Worksheet extends ExcelBase
{
    private ?int $nrOfChunks = null;

    public function __construct(string $sheetName)
    {
        $this->sheetName = $sheetName;
    }

    /**
     * @param array{worksheetName: string, lastColumnLetter: string, lastColumnIndex: int, totalRows: int, totalColumns: int} $info
     * @internal
     */
    public function setWorksheetInfo(array $info): void
    {
        $this->sheetName        = $info['worksheetName'];
        $this->lastColumnLetter = $info['lastColumnLetter'];
        $this->lastColumnIndex  = $info['lastColumnIndex'];
        $this->totalRows        = $info['totalRows'];
        $this->totalColumns     = $info['totalColumns'];
    }

    public function setNumberOfHeaderRows(int $numberOfHeaderRows): void
    {
        $this->numberOfHeaderRows = $numberOfHeaderRows;
    }

    public function setColumns(Column ...$BSExcelColumns): void
    {
        foreach ($BSExcelColumns as $BSExcelColumn) {
            if (!in_array($BSExcelColumn, $this->columnsToParse, true)) {
                $this->columnsToParse[] = $BSExcelColumn;
            }
        }
    }

    public function getFilter(int $chunk = 1): IReadFilter
    {
        $filterArr = array();
        if ($this->nrOfChunks > 1) {
            if (!empty($this->columnsToParse)) {
                foreach ($this->columnsToParse as $column) {
                    $filterArr[$column->columnString] = true;
                }
                $filter = new Filter\DefineWorksheetRowsColumns(array($this->sheetName => true), (($this->chunkSize * $chunk) + 1), ($this->chunkSize * $chunk), $filterArr);
            } else {
                $filter = new Filter\DefineWorksheetRows(array($this->sheetName => true), (($this->chunkSize * $chunk) + 1), ($this->chunkSize * $chunk));
            }
        } else {
            if (!empty($this->columnsToParse)) {
                foreach ($this->columnsToParse as $column) {
                    $filterArr[$column->columnString] = true;
                }
                $filter = new Filter\DefineWorksheetColumns(array($this->sheetName => true), $filterArr);
            } else {
                $filter = new Filter\DefineWorksheet(array($this->sheetName => true));
            }
        }
        return $filter;
    }

    public function getNumberOfChunks(): int
    {
        if ($this->nrOfChunks === null) {
            $this->nrOfChunks = $this->chunkSize === null ? 1 : (($this->totalRows - ($this->totalRows % $this->chunkSize)) / $this->chunkSize) + ((($this->totalRows % $this->chunkSize) === 0) ? 0 : 1);
        }
        return $this->nrOfChunks;
    }
}
