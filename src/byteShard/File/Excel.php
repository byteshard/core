<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File;

use byteShard\Internal\File\ExcelBase;
use byteShard\File\Excel\Worksheet;
use byteShard\File\Excel\Filter;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use stdClass;

class Excel extends ExcelBase
{
    private string  $filename;
    private ?string $fileType   = null;
    private array   $worksheets = [];
    /* @var $reader IReader */
    private IReader $reader;

    public function __construct(string $filename, Worksheet ...$worksheets)
    {
        if (file_exists($filename)) {
            $this->filename = $filename;
            $this->fileType = IOFactory::identify($this->filename);
            $this->reader   = IOFactory::createReader($this->fileType);
            if ($this->reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Csv || $this->reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Xls || $this->reader instanceof Xlsx) {
                $this->reader->setReadDataOnly($this->dontImportCellFormat);
                foreach ($this->reader->listWorksheetInfo($this->filename) as $sheetIndex => $sheetInfo) {
                    foreach ($worksheets as $key => $worksheet) {
                        if (is_numeric($worksheet->sheetName)) {
                            if ($sheetIndex === $worksheet->sheetName) {
                                $this->worksheets[$sheetIndex] = $worksheet;
                                $worksheet->setWorksheetInfo($sheetInfo);
                                unset($worksheets[$key]);
                            }
                        } elseif ($sheetInfo['worksheetName'] === $worksheet->sheetName) {
                            $this->worksheets[$sheetIndex] = $worksheet;
                            $worksheet->setWorksheetInfo($sheetInfo);
                            unset($worksheets[$key]);
                        }
                    }
                }
            } else {
                print 'incorrect file type';
                exit;
            }
        } else {
            print 'file does not exist';
            exit;
        }
    }

    public function getArray(): stdClass
    {
        $result = new stdClass();
        if (!empty($this->worksheets)) {
            $this->getColumnIndizes();
            foreach ($this->worksheets as $sheetIndex => $worksheet) {
                /* @var $worksheet Excel\Worksheet */
                for ($chunk = 1, $chunk_max = $worksheet->getNumberOfChunks(); $chunk <= $chunk_max; $chunk++) {
                    $filter = $worksheet->getFilter($chunk);
                    $this->reader->setReadFilter($filter);
                    $phpExcel = $this->reader->load($this->filename);
                    /* @var $phpExcel */
                    $sheet = $phpExcel->getSheet($sheetIndex);
                    $start = $worksheet->numberOfHeaderRows + 1;
                    for ($row = $start; $row <= $worksheet->totalRows; $row++) {
                        $rowData = new stdClass();
                        foreach ($worksheet->columnsToParse as $col) {
                            if ($col->date === true) {
                                $tmpVal = $sheet->getCellByColumnAndRow($col->columnIndex, $row)->getValue();
                                if ($tmpVal !== null && !empty($tmpVal)) {
                                    $rowData->{($col->propertyName !== null ? $col->propertyName : $col->columnString)} = Date::excelToTimestamp($sheet->getCellByColumnAndRow($col->columnIndex, $row)->getValue());
                                } else {
                                    $rowData->{($col->propertyName !== null ? $col->propertyName : $col->columnString)} = null;
                                }
                            } elseif ($col->calculatedValue === false) {
                                $rowData->{($col->propertyName !== null ? $col->propertyName : $col->columnString)} = $sheet->getCellByColumnAndRow($col->columnIndex, $row)->getValue();
                            } else {
                                $rowData->{($col->propertyName !== null ? $col->propertyName : $col->columnString)} = $sheet->getCellByColumnAndRow($col->columnIndex, $row)->getCalculatedValue();
                            }
                        }
                        $result->{$worksheet->sheetName}[$row] = $rowData;
                        unset($rowData);
                    }
                }
            }
        } else {
            print "no worksheets defined";
        }
        return $result;
    }

    private function getColumnIndizes(): void
    {
        $this->reader->setReadDataOnly(false);
        foreach ($this->worksheets as $sheetIndex => $BSWorksheet) {
            if (count($BSWorksheet->columnsToParse) > 0) {
                $maxRow = 0;
                foreach ($BSWorksheet->columnsToParse as $column) {
                    if ($column->columnArray !== null) {
                        foreach ($column->columnArray as $row => $string) {
                            $maxRow = max($maxRow, $row);
                        }
                    }
                }
                if ($maxRow > 0) {
                    $this->reader->setReadFilter(new Filter\DefineWorksheetRows(array($BSWorksheet->sheetname => true), 1, $maxRow));
                    /* @var $sheet */
                    $sheet       = $this->reader->load($this->filename)->getSheet($sheetIndex);
                    $mergedCells = $sheet->getMergeCells();
                    foreach ($BSWorksheet->columnsToParse as $BSColumn) {
                        if ($BSColumn->columnArray !== null) {
                            for ($column = 0; $column <= $BSWorksheet->lastColumnIndex; $column++) {
                                $match = true;
                                foreach ($BSColumn->columnArray as $row => $searchString) {
                                    $cell = $sheet->getCellByColumnAndRow($column, $row);
                                    if (!empty($mergedCells)) {
                                        $cellIsPartOfMergedCells = false;
                                        foreach ($mergedCells as $range) {
                                            if ($cell->isInRange($range)) {
                                                $cellIsPartOfMergedCells = true;
                                            }
                                        }
                                        if ($cellIsPartOfMergedCells === true) {
                                            $tmp = $cell->getValue();
                                            if ($tmp !== '' && $tmp !== null) {
                                                $cellString[$row] = $tmp;
                                            }
                                            unset($tmp);
                                        } else {
                                            $cellString[$row] = $cell->getValue();
                                        }
                                    } else {
                                        $cellString[$row] = $cell->getValue();
                                    }
                                    if (isset($cellString) && array_key_exists($row, $cellString) && $cellString[$row] !== $searchString) {
                                        $match = false;
                                        break;
                                    }
                                }
                                if ($match === true) {
                                    $BSColumn->columnIndex  = $column;
                                    $BSColumn->columnString = Coordinate::stringFromColumnIndex($column);
                                    unset($cellString);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->reader->setReadDataOnly($this->dontImportCellFormat);
    }
}
