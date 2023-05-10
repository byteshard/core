<?php
/*
 * Erzeugt ein Export-File von 2 Arrays: Daten + Format
 */
namespace byteShard\Internal\Export\ClientExport;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DirectExport
{
    private        $dataFormat;
    private        $dataSrc;
    private string $sheetName      = 'Export';
    private string $title          = 'Export';
    private string $filename       = 'Export';
    private string $creator        = 'bespin';
    private string $lastModifiedBy = 'bepsin';
    private string $subject        = 'Export';
    private        $exportObj;
    private        $exportSheet;

    /*
     * Konstruktor der Klasse
     */
    public function __construct($dataFormat, $dataSrc)
    {
        $this->dataFormat = $dataFormat;
        $this->dataSrc    = $dataSrc;
    }

    /*
     * Sheetname übernehmen und anhand Excel Restriktionen kürzen
     */
    public function setSheetName($name): void
    {
        // Rename sheet: auf 30 Stellen und bestimmte Zeichen begrenzen
        $mySheetName     = $name;
        $mySheetName     = preg_replace("/[^a-zA-Z0-9ÄÖÜäöüß\\040\\.\\-\\_,;<>|+-=!\"§$%&()?#'~^]/", "", $mySheetName);
        $mySheetName     = preg_replace("/[\\/\\\\]/", "_", $mySheetName);
        $mySheetName     = substr($mySheetName, 0, ((strlen($mySheetName) < 31) ? strlen($mySheetName) : 30));
        $this->sheetName = $mySheetName;
    }

    /*
     * Weitere XLS-Definitionen setzen
     */
    public function setProperties($title, $creator, $lastModifiedBy, $subject): void
    {
        $this->creator        = $creator;
        $this->lastModifiedBy = $lastModifiedBy;
        $this->title          = $title;
        $this->subject        = $subject;
    }

    /**
     * Excel Workbook erzeugen
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function createXLS(): void
    {
        $this->exportObj = new Spreadsheet();
        $this->exportObj->setActiveSheetIndex(0);
        $this->exportSheet = $this->exportObj->getActiveSheet();
        /// Set properties
        $this->exportObj->getProperties()->setCreator($this->creator);
        $this->exportObj->getProperties()->setLastModifiedBy($this->lastModifiedBy);
        $this->exportObj->getProperties()->setTitle($this->title);
        $this->exportObj->getProperties()->setSubject($this->subject);
        $this->exportSheet->setTitle($this->sheetName);

        //Standard Style setzen
        $this->setDefaultStyle();

        $starttime = time();
        //Format Array anwenden
        $this->applyFormatFromArr();
        //Inhalt schreiben
        $this->insertXLSData();

        //Filter setzen?
        if (isset($this->dataFormat['autoFilter'])) {
            $this->setAutoFilter();
        }

        $endtime = time();
        //print "Dauer: ".($endtime-$starttime)." sec";exit;
    }

    public function setFileName($filename): void
    {
        $this->filename = $filename;
    }

    /**
     * Ausgabe des Export Files
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function getExportFile(): void
    {
        $objWriter                = IOFactory::createWriter($this->exportObj, 'Excel2007');
        $GLOBALS['output_buffer'] = ob_get_clean();
        //$html Ausgabe erfolgt in exportGRID.php
        ob_start();
        $objWriter->save('php://output');
        $html = ob_get_clean();

        header("Content-Disposition: attachment; filename=\"".$this->filename.".xlsx\";");
        header("Pragma: public");                                          // required
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");                  // Date in the past
        header('Cache-Control: no-store, no-cache, must-revalidate');      // HTTP/1.1
        header('Cache-Control: pre-check=0, post-check=0, max-age=0');     // HTTP/1.1
        header("Cache-Control: private", false);                           // required for certain browsers
        header("Content-Type: application/vnd.ms-excel; charset=\"utf-8\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Type: application/octet-stream");

        print($html);
    }

    /*
     * Das Datenarray incl. Styles abarbeiten und in XLS Objekt schreiben
     */
    private function insertXLSData(): void
    {
        //Übergebenes Daten-Array verarbeiten
        foreach ($this->dataSrc as $rowIdx => $row) {
            foreach ($row as $colIdx => $cell) {
                $value = $cell['value'];

                // Zellinhalt schreiben
                //$this->exportSheet->getCellByColumnAndRow($colIdx-1,$rowIdx)->setValue($value);
                $this->exportSheet->getCellByColumnAndRow($colIdx - 1, $rowIdx)->setValueExplicit($value, DataType::TYPE_STRING);
                //Folgende 2 Aufrufe sind 10sec langsamer bei 10000 Zeilen * 50 Spalten
                //$this->exportSheet->setCellValueExplicitByColumnAndRow($colIdx-1,$rowIdx, $value, PHPExcel_Cell_DataType::TYPE_STRING);
                //$this->exportSheet->setCellValueByColumnAndRow($colIdx-1,$rowIdx, $value);

                //Wenn ausser dem Value und GroupingLevel weitere Parameter für die Zelle angegeben sind, wird das Styleobjekt benötigt
                if (count($cell) > 1) {
                    $cellStyleArray = array();
                    //Schriftfarbe festlegen
                    if (isset($cell['fontColorIdx'])) {
                        $cellStyleArray['font']['color'] = array('argb' => Color::indexedColor($cell['fontColorIdx'])->getRGB());
                    }
                    //Schriftgröße festlegen
                    if (isset($cell['fontSize'])) {
                        $cellStyleArray['font']['size'] = $cell['fontSize'];
                    }
                    //Fett festlegen
                    if (isset($cell['bold'])) {
                        $cellStyleArray['font']['bold'] = true;
                    }
                    //Zeilenumbruch festlegen
                    if (isset($cell['wrap'])) {
                        $cellStyleArray['alignment']['wrap'] = $cell['wrap'];
                    }
                    //Vertikale Ausrichtung festlegen
                    if (isset($cell['valign'])) {
                        $cellStyleArray['alignment']['vertical'] = $cell['valign'];
                    }
                    //Horizontale Ausrichtung festlegen
                    if (isset($cell['halign'])) {
                        $cellStyleArray['alignment']['horizontal'] = $cell['halign'];
                    }
                    //Zell-Hintergrundfarbe festlegen
                    if (isset($cell['bgColorIdx'])) {
                        $cellStyleArray['fill']['type']  = Fill::FILL_SOLID;
                        $cellStyleArray['fill']['color'] = array('argb' => Color::indexedColor($cell['bgColorIdx'])->getRGB());
                    }
                    //print("<pre>");print_r($cellStyleArray);print("<br />".($colIdx-1)." ".$rowIdx."</pre>");
                    //Style-Array auf Zelle anwenden
                    $this->exportSheet->getStyleByColumnAndRow($colIdx - 1, $rowIdx)->applyFromArray($cellStyleArray);
                }
            }
        }
    }

    /*
     * Übergebenes Format-Array auf XLS Objekt anwenden
     */
    private function applyFormatFromArr(): void
    {
        //Gruppierungen setzen
        if (isset($this->dataFormat['grouping'])) {
            //Gruppierungsknoten standardmässig nach oben bzw. links setzen
            $this->exportSheet->setShowSummaryBelow(false);
            $this->exportSheet->setShowSummaryRight(false);

            //Spalten Gruppierungen setzen
            if (isset($this->dataFormat['grouping']['col'])) {
                foreach ($this->dataFormat['grouping']['col'] as $colIdx => $level) {
                    $this->setGrouping('col', $colIdx, $level);
                }
            }

            //Zeilen Gruppierungen setzen
            if (isset($this->dataFormat['grouping']['row'])) {
                foreach ($this->dataFormat['grouping']['row'] as $rowIdx => $level) {
                    $this->setGrouping('row', $rowIdx, $level);
                }
            }
        }

        //Spaltenbreiten einer Spalte
        if (isset($this->dataFormat['colWidth'])) {
            foreach ($this->dataFormat['colWidth'] as $colIdx => $colWidth) {
                $this->exportSheet->getColumnDimensionByColumn($colIdx)->setWidth($colWidth);
            }
        }
        //Zeilenhöhen einer Zeile
        if (isset($this->dataFormat['rowHeight'])) {
            foreach ($this->dataFormat['rowHeight'] as $rowIdx => $rowHeight) {
                $this->exportSheet->getRowDimension($rowIdx + 1)->setRowHeight($rowHeight);
            }
        }

        //Zellen-Merge einer Range
        if (isset($this->dataFormat['merge'])) {
            foreach ($this->dataFormat['merge'] as $mergeParam) {
                $this->exportSheet->mergeCellsByColumnAndRow($mergeParam['from']['col'] - 1, $mergeParam['from']['row'], $mergeParam['to']['col'] - 1, $mergeParam['to']['row']);
            }
        }

        //Spalten/Zeilen Fixierung setzen
        if (isset($this->dataFormat['freezePane'])) {
            $row = (isset($this->dataFormat['freezePane']['row'])) ? $this->dataFormat['freezePane']['row'] : 1;
            $col = (isset($this->dataFormat['freezePane']['col'])) ? $this->dataFormat['freezePane']['col'] : 1;
            $this->exportSheet->freezePaneByColumnAndRow($col - 1, $row);
        }

        //Format welches per Range festgelegt wird
        if (isset($this->dataFormat['rangeFormat'])) {
            foreach ($this->dataFormat['rangeFormat'] as $formatParam) {
                $styleArray = array();

                //Border definiert
                if (isset($formatParam['borderColorIdx'])) {
                    $styleArray['borders'] = array(
                        'allborders' => array(
                            'style' => Border::BORDER_THIN,
                            'color' => array('argb' => Color::indexedColor($formatParam['borderColorIdx'])->getRGB())
                        )
                    );
                }

                //Font Color definiert
                if (isset($formatParam['fontColorIdx'])) {
                    $styleArray['font']['color'] = array('argb' => Color::indexedColor($formatParam['fontColorIdx'])->getRGB());
                }

                //Font Size definiert
                if (isset($formatParam['fontSize'])) {
                    $styleArray['font']['size'] = $formatParam['fontSize'];
                }

                //Background Color definiert
                if (isset($formatParam['bgColorIdx'])) {
                    $styleArray['fill'] = array(
                        'type'  => Fill::FILL_SOLID,
                        'color' => array('argb' => Color::indexedColor($formatParam['bgColorIdx'])->getRGB())
                    );
                }

                //Zeilenumbruch definiert
                if (isset($formatParam['wrap'])) {
                    $styleArray['alignment']['wrap'] = $formatParam['wrap'];
                }

                //Vertikale Ausrichtung definiert
                if (isset($formatParam['valign'])) {
                    $styleArray['alignment']['vertical'] = $formatParam['valign'];
                }

                //Horizontale Ausrichtung definiert
                if (isset($formatParam['halign'])) {
                    $styleArray['alignment']['horizontal'] = $formatParam['halign'];
                }

                //Fett definiert
                if (isset($formatParam['bold'])) {
                    $styleArray['font']['bold'] = true;
                }

                //Format auf Range anwenden
                $this->exportSheet->getStyle(Coordinate::stringFromColumnIndex($formatParam['from']['col'] - 1).$formatParam['from']['row'].':'.Coordinate::stringFromColumnIndex($formatParam['to']['col'] - 1).$formatParam['to']['row'])->applyFromArray($styleArray);
            }
        }
    }

    /*
     * Gruppierungen festlegen für Zeilen oder Spalten
     * @param string type: row oder col
     * @param integer idx
     * @param integer level
     */
    private function setGrouping($type, $idx, $level): void
    {
        switch ($type) {
            case 'row':
                //Gruppierung für Zeile setzen
                $this->exportSheet->getRowDimension($idx)->setOutlineLevel($level);
                break;
            case 'col':
                $this->exportSheet->getColumnDimensionByColumn($idx)->setOutlineLevel($level);
                break;
        }
    }

    /*
     * Standard Formatierung vorgeben
     */
    private function setDefaultStyle(): void
    {
        /// Styles definieren
        $this->exportSheet->getSheetView()->setZoomScale(75);
        $this->exportSheet->getDefaultStyle()->getFont()->setName('Arial');
        $this->exportSheet->getDefaultStyle()->getFont()->setSize(9);
        $this->exportSheet->getDefaultColumnDimension()->setWidth(20);
        $this->exportSheet->getDefaultRowDimension()->setRowHeight(22);//Nicht funktionsfähig
        $this->exportSheet->getDefaultStyle()->getAlignment()->setWrapText(true);
        $this->exportSheet->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $this->exportSheet->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function setAutoFilter(): void
    {
        $firstRow = (isset($this->dataFormat['autoFilter']['from']['row']) && is_numeric($this->dataFormat['autoFilter']['from']['row']) ? $this->dataFormat['autoFilter']['from']['row'] : 1);
        $lastRow  = (isset($this->dataFormat['autoFilter']['to']['row']) && is_numeric($this->dataFormat['autoFilter']['to']['row']) ? $this->dataFormat['autoFilter']['to']['row'] : 1);
        $firstCol = (isset($this->dataFormat['autoFilter']['from']['col']) && is_numeric($this->dataFormat['autoFilter']['from']['col']) ? $this->dataFormat['autoFilter']['from']['col'] : 1);
        $lastCol  = (isset($this->dataFormat['autoFilter']['to']['col']) && is_numeric($this->dataFormat['autoFilter']['to']['col']) ? $this->dataFormat['autoFilter']['to']['col'] : Coordinate::columnIndexFromString($this->exportSheet->getHighestColumn()));

        $this->exportSheet->setAutoFilterByColumnAndRow($firstCol - 1, $firstRow, $lastCol - 1, $lastRow);
    }
}
