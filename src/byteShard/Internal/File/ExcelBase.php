<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\File;

use byteShard\File\Excel\Column;

/**
 * Class ExcelBase
 * @package byteShard\Internal\File
 */
abstract class ExcelBase
{
    protected string $sheetName;
    protected string $lastColumnLetter;
    protected int    $lastColumnIndex;
    protected int    $totalRows;
    protected int    $totalColumns;
    protected bool   $dontImportCellFormat = true;
    /** @var Column[] */
    protected array $columnsToParse     = [];
    protected ?int  $chunkSize          = null;
    protected int   $numberOfHeaderRows = 0;

    //ExcelColumns properties
    protected string  $columnString;
    protected int     $columnIndex;
    protected array   $columnArray     = [];
    protected bool    $calculatedValue = false;
    protected bool    $date            = false;
    protected ?string $propertyName    = null;
}
