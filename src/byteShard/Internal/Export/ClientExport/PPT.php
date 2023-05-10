<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Export\ClientExport;

use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Writer\PowerPoint2007\LayoutPack\TemplateBased;

class PPT
{
    public const TARGET_BROWSER = 'browser';
    public const TARGET_FILE = 'file';

    // PowerPoint File
    protected ?PhpPresentation $ppt        = null;
    protected                  $currentSlide;
    protected                  $firstSlide = true;

    // PPT Properties
    private string $creator     = '';
    private string $title       = '';
    private string $subject     = '';
    private string $description = '';
    private string $keywords    = '';
    private string $category    = '';

    // Fonts
    protected $defaultHeaderFont      = 'Helvetica';
    protected $defaultHeaderFontSize  = 30;
    protected $defaultHeaderFontBold  = false;
    protected $defaultHeaderFontColor = null;
    protected $defaultBodyFont        = 'Helvetica';
    protected $defaultBodyFontSize    = 16;
    protected $defaultBodyFontBold    = false;
    protected $defaultBodyFontColor   = null;

    // Table
    private       $defaultTableFillType        = Fill::FILL_SOLID;
    private       $defaultTableHeaderFont      = 'Helvetica';
    private       $defaultTableHeaderFontSize  = 12;
    private       $defaultTableHeaderFontBold  = true;
    private       $defaultTableHeaderFontColor = null;
    private       $defaultTableBodyFont        = 'Helvetica';
    private       $defaultTableBodyFontSize    = 12;
    private bool  $defaultTableBodyFontBold    = false;
    private       $defaultTableBodyFontColor   = null;
    private float $defaultTableMarginTop       = 0.1;
    private float $defaultTableMarginBottom    = 0.1;
    private float $defaultTableMarginRight     = 0.2;
    private float $defaultTableMarginLeft      = 0.2;


    // Slide Master
    private ?Rect $slideMaster = null;
    private ?Rect $defaultHeaderRect;
    private ?Rect $defaultBodyRect;
    private ?Rect $defaultTableRect;

    // File
    private string $target   = self::TARGET_BROWSER;
    private string $filename = 'test2.pptx';
    private string $path     = '';

    public function ppt(): void
    {
        $this->ppt               = new PhpPresentation();
        $this->defaultHeaderRect = new Rect(2.5, 23.2, 1.1, 1.02);
        $this->defaultBodyRect   = new Rect(13.8, 23.2, 1.1, 3.95);
        $this->defaultTableRect  = new Rect(7.19, 23.82, 0.9, 4.92);
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function setFilename(string $filename): void
    {
        $filename = preg_replace("/[^a-zA-Z0-9ÄÖÜäöüß\\040\\.\\-\\_,;<>|+-=!\"§$%&()?#'~^]/", "", $filename) ?? '';
        $filename = preg_replace("/[\\/\\\\]/", "_", $filename) ?? '';
        $filename = substr($filename, 0, ((strlen($filename) < 31) ? strlen($filename) : 30));
        if (substr_count($filename, '.pptx') === 0) {
            $filename = $filename.'.pptx';
        }
        $this->filename = $filename;
    }

    /**
     * @API
     */
    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    /**
     * @API
     */
    public function setDefaultHeaderRect(Rect $rect): void
    {
        $this->defaultHeaderRect = $rect;
    }

    public function setDefaultBodyRect(Rect $rect): void
    {
        $this->defaultBodyRect = $rect;
    }

    public function setDefaultTableRect(Rect $rect): void
    {
        $this->defaultTableRect = $rect;
    }

    private function setProperties(): void
    {
        $this->ppt->getProperties()->setCreator($this->creator)->setLastModifiedBy($this->creator)->setTitle($this->title)->setSubject($this->subject)->setDescription($this->description)->setKeywords($this->keywords)->setCategory($this->category);
    }

    public function setCreator(string $creator): void
    {
        $this->creator = $creator;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setKeywords($keywords): void
    {
        $this->keywords = $keywords;
    }

    public function setCategory($category): void
    {
        $this->category = $category;
    }

    public function setDefaultHeaderFont($fontName, $size = null): void
    {
        $this->defaultHeaderFont = $fontName;
        if (!is_null($size)) {
            $this->defaultHeaderFontSize = $size;
        }
    }

    public function setDefaultBodyFont($fontName, $size = null): void
    {
        $this->defaultBodyFont = $fontName;
        if (!is_null($size)) {
            $this->defaultBodyFontSize = $size;
        }
    }

    public function setDefaultTableHeaderFont($fontName, $size = null): void
    {
        $this->defaultTableHeaderFont = $fontName;
        if (!is_null($size)) {
            $this->defaultTableHeaderFontSize = $size;
        }
    }

    public function setDefaultTableBodyFont($fontName, $size = null): void
    {
        $this->defaultTableBodyFont = $fontName;
        if (!is_null($size)) {
            $this->defaultTableBodyFontSize = $size;
        }
    }

    public function setSlideMaster($slideMasterPathFileName): void
    {
        $this->slideMaster = $slideMasterPathFileName;
    }

    // PPT Objects
    public function createTextField($text = null, $rect = null, $textFormat = null, $shapeFormat = null): void
    {
        if (is_null($rect)) {
            $shape = $this->currentSlide->createRichTextShape();
            $shape->setHeight($this->defaultBodyRect->height)->setWidth($this->defaultBodyRect->width)->setOffsetX($this->defaultBodyRect->offsetX)->setOffsetY($this->defaultBodyRect->offsetY);
        } else {
            $shape = $this->currentSlide->createRichTextShape();
            $shape->setHeight($rect->height)->setWidth($rect->width)->setOffsetX($rect->offsetX)->setOffsetY($rect->offsetY);
        }
        if (is_null($shapeFormat)) {
            $shapeFormat = new ShapeFormat();
        }
        $shapeFormat->applyFormat($shape);
        if (!is_null($text)) {
            $text = $shape->createTextRun($text);
            if (is_null($textFormat)) {
                $textFormat = new TextFormat($this->defaultBodyFontSize, $this->defaultBodyFontColor, $this->defaultBodyFont, $this->defaultBodyFontBold);
            }
            $textFormat->applyFormat($text);
        }
    }

    public function createFooterTextField($text = null, $rect = null, $textFormat = null, $shapeFormat = null, $insetLeft = null): void
    {
        if (is_null($rect)) {
            $shape = $this->currentSlide->createRichTextShape()->setHeight(0.85)->setWidth(17.2)->setOffsetX(1.1)->setOffsetY(18.18);
        } else {
            $shape = $this->currentSlide->createRichTextShape()->setHeight($rect->height)->setWidth($rect->width)->setOffsetX($rect->offsetX)->setOffsetY($rect->offsetY);
        }
        $shape->setInsetTop(0.3);
        if (!is_null($insetLeft)) {
            $shape->setInsetLeft($insetLeft);
        } else {
            $shape->setInsetLeft(0);
        }

        if (is_null($shapeFormat)) {
            $shapeFormat = new ShapeFormat(Alignment::HORIZONTAL_LEFT, Alignment::VERTICAL_CENTER);
        }
        $shapeFormat->applyFormat($shape);
        if (!is_null($text)) {
            $text = $shape->createTextRun($text);
            $text->getText();
            if (is_null($textFormat)) {
                $textFormat = new TextFormat(9, null, 'CorpoS', false);
            }
            $textFormat->applyFormat($text);
        }
    }

    public function createHeaderTextField($text = null, $rect = null, $textFormat = null, $shapeFormat = null, $insetLeft = null)
    {
        if (is_null($rect)) {
            $shape = $this->currentSlide->createRichTextShape()->setHeight($this->defaultHeaderRect->height)->setWidth($this->defaultHeaderRect->width)->setOffsetX($this->defaultHeaderRect->offsetX)->setOffsetY($this->defaultHeaderRect->offsetY);
        } else {
            $shape = $this->currentSlide->createRichTextShape()->setHeight($rect->height)->setWidth($rect->width)->setOffsetX($rect->offsetX)->setOffsetY($rect->offsetY);
        }
        $shape->setInsetTop(Rect::convert(0.3));
        $shape->setInsetRight(Rect::convert(0.5));
        $shape->setInsetBottom(Rect::convert(1));
        if (!is_null($insetLeft)) {
            $shape->setInsetLeft($insetLeft);
        } else {
            $shape->setInsetLeft(Rect::convert(0.5));
        }

        if (is_null($shapeFormat)) {
            $shapeFormat = new ShapeFormat();
        }
        $shapeFormat->applyFormat($shape);
        if (!is_null($text)) {
            $text = $shape->createTextRun($text);
            $text->getText();
            if (is_null($textFormat)) {
                $textFormat = new TextFormat($this->defaultHeaderFontSize, $this->defaultHeaderFontColor, $this->defaultHeaderFont, $this->defaultHeaderFontBold);
            }
            $textFormat->applyFormat($text);
        }
        return $shape;
    }

    public function createTable(array $array, $rect = null)
    {
        $col = 0;
        reset($array);
        $firstRow = key($array);
        // TODO: Colspan beachten
        if (is_array($array[$firstRow]) && !empty($array[$firstRow])) {
            foreach ($array[$firstRow] as $idx => $val) {
                if (is_numeric($idx)) {
                    $col++;
                }
            }
        }
        if ($col > 0) {
            $shape = $this->currentSlide->createTableShape($col);
            if (is_null($rect)) {
                $shape->setHeight($this->defaultTableRect->height);
                $shape->setWidth($this->defaultTableRect->width);
                $shape->setOffsetX($this->defaultTableRect->offsetX);
                $shape->setOffsetY($this->defaultTableRect->offsetY);
            } else {
                $shape->setHeight($rect->height);
                $shape->setWidth($rect->width);
                $shape->setOffsetX($rect->offsetX);
                $shape->setOffsetY($rect->offsetY);
            }


            foreach ($array as $rowIdx => $rowContent) {
                // ROW
                $row = $shape->createRow();
                if (array_key_exists('row', $rowContent)) {
                    $fill = $row->getFill();
                    if (array_key_exists('color', $rowContent['row'])) {
                        $fill->setStartColor(new Color($rowContent['row']['color']));
                        if (!array_key_exists('fill', $rowContent['row'])) {
                            $rowContent['row']['fill'] = $this->defaultTableFillType;
                        }
                    }
                    if (array_key_exists('fill', $rowContent['row'])) {
                        // TODO: alle Fill Types abbilden
                        switch ($rowContent['row']['fill']) {
                            case Fill::FILL_SOLID:
                                $fill->setFillType(Fill::FILL_SOLID);
                                break;
                        }
                    }
                    if (array_key_exists('height', $rowContent['row'])) {
                        $row->setHeight($rowContent['row']['height']);
                    }
                    unset($fill);
                    unset($rowContent['row']);
                }
                foreach ($rowContent as $cellIdx => $cellContent) {
                    // CELL
                    $cell = $row->nextCell();
                    if (array_key_exists('value', $cellContent) && $cellContent['value'] != '') {
                        // Font Size
                        if ($rowIdx == $firstRow) {
                            $size = $this->defaultTableHeaderFontSize;
                        } else {
                            $size = $this->defaultTableBodyFontSize;
                        }
                        if (array_key_exists('font', $cellContent) && array_key_exists('size', $cellContent['font'])) {
                            $size = $cellContent['font']['size'];
                        }
                        $text = $cell->createTextRun($cellContent['value']);
                        //$text->setInsetBottom(100);

                        $font = $text->getFont()->setSize($size);

                        // Bold
                        if ($rowIdx == $firstRow) {
                            $bold = $this->defaultTableHeaderFontBold;
                        } else {
                            $bold = $this->defaultTableBodyFontBold;
                        }
                        if (array_key_exists('font', $cellContent) && array_key_exists('bold', $cellContent['font'])) {
                            $bold = $cellContent['font']['bold'];
                        }
                        if ($bold) {
                            $font->setBold(true);
                        }

                        // Font Name
                        if ($rowIdx == $firstRow) {
                            $fontName = $this->defaultTableHeaderFont;
                        } else {
                            $fontName = $this->defaultTableBodyFont;
                        }
                        if (array_key_exists('font', $cellContent) && array_key_exists('name', $cellContent['font'])) {
                            $fontName = $cellContent['font']['name'];
                        }
                        $font->setName($fontName);

                        // Font Color
                        $fontColor = null;
                        if ($rowIdx == $firstRow) {
                            if (!is_null($this->defaultTableHeaderFontColor)) {
                                $fontColor = $this->defaultTableHeaderFontColor;
                            }
                        } else {
                            if (!is_null($this->defaultTableBodyFontColor)) {
                                $fontColor = $this->defaultTableBodyFontColor;
                            }
                        }
                        if (array_key_exists('font', $cellContent) && array_key_exists('color', $cellContent['font'])) {
                            $fontColor = $cellContent['font']['color'];
                        }
                        if (!is_null($fontColor)) {
                            $font->setColor(new Color($fontColor));
                        }
                        $cell->getActiveParagraph()->getAlignment()->setMarginLeft(0);
                        $cell->getActiveParagraph()->getAlignment()->setMarginRight(0);
                        if (array_key_exists('vAlign', $cellContent) || array_key_exists('hAlign', $cellContent)) {
                            if (array_key_exists('vAlign', $cellContent)) {
                                switch ($cellContent['vAlign']) {
                                    case 'top':
                                        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                                        break;
                                    case 'center':
                                        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                                        break;
                                    case 'bottom':
                                        $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
                                        break;
                                }
                            }
                            if (array_key_exists('hAlign', $cellContent)) {
                                switch ($cellContent['hAlign']) {
                                    case 'left':
                                        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                                        break;
                                    case 'center':
                                        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                                        break;
                                    case 'right':
                                        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                                        break;
                                    case 'justified':
                                        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
                                        break;
                                    case 'distributed':
                                        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_DISTRIBUTED);
                                        break;
                                }
                            }
                        }
                    } elseif (is_array($cellContent) && !array_key_exists('value', $cellContent)) {
                        foreach ($cellContent as $cellSubcontent) {
                            if (is_array($cellSubcontent) && array_key_exists('value', $cellSubcontent) && $cellSubcontent['value'] != '') {
                                // Font Size
                                if ($rowIdx == $firstRow) {
                                    $size = $this->defaultTableHeaderFontSize;
                                } else {
                                    $size = $this->defaultTableBodyFontSize;
                                }
                                if (array_key_exists('font', $cellSubcontent) && array_key_exists('size', $cellSubcontent['font'])) {
                                    $size = $cellSubcontent['font']['size'];
                                }
                                $text = $cell->createTextRun($cellSubcontent['value']);
                                //$text->setInsetBottom(100);

                                $font = $text->getFont()->setSize($size);

                                // Bold
                                if ($rowIdx == $firstRow) {
                                    $bold = $this->defaultTableHeaderFontBold;
                                } else {
                                    $bold = $this->defaultTableBodyFontBold;
                                }
                                if (array_key_exists('font', $cellSubcontent) && array_key_exists('bold', $cellSubcontent['font'])) {
                                    $bold = $cellSubcontent['font']['bold'];
                                }
                                if ($bold) {
                                    $font->setBold(true);
                                }

                                // Font Name
                                if ($rowIdx == $firstRow) {
                                    $fontName = $this->defaultTableHeaderFont;
                                } else {
                                    $fontName = $this->defaultTableBodyFont;
                                }
                                if (array_key_exists('font', $cellSubcontent) && array_key_exists('name', $cellSubcontent['font'])) {
                                    $fontName = $cellSubcontent['font']['name'];
                                }
                                $font->setName($fontName);

                                // Font Color
                                $fontColor = null;
                                if ($rowIdx == $firstRow) {
                                    if (!is_null($this->defaultTableHeaderFontColor)) {
                                        $fontColor = $this->defaultTableHeaderFontColor;
                                    }
                                } else {
                                    if (!is_null($this->defaultTableBodyFontColor)) {
                                        $fontColor = $this->defaultTableBodyFontColor;
                                    }
                                }
                                if (array_key_exists('font', $cellSubcontent) && array_key_exists('color', $cellSubcontent['font'])) {
                                    $fontColor = $cellSubcontent['font']['color'];
                                }
                                if (!is_null($fontColor)) {
                                    $font->setColor(new Color($fontColor));
                                }
                                $cell->getActiveParagraph()->getAlignment()->setMarginLeft(0);
                                $cell->getActiveParagraph()->getAlignment()->setMarginRight(0);
                                if (array_key_exists('vAlign', $cellSubcontent) || array_key_exists('hAlign', $cellSubcontent)) {
                                    if (array_key_exists('vAlign', $cellSubcontent)) {
                                        switch ($cellSubcontent['vAlign']) {
                                            case 'top':
                                                $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                                                break;
                                            case 'center':
                                                $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                                                break;
                                            case 'bottom':
                                                $cell->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
                                                break;
                                        }
                                    }
                                    if (array_key_exists('hAlign', $cellSubcontent)) {
                                        switch ($cellSubcontent['hAlign']) {
                                            case 'left':
                                                $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                                                break;
                                            case 'center':
                                                $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                                                break;
                                            case 'right':
                                                $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                                                break;
                                            case 'justified':
                                                $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
                                                break;
                                            case 'distributed':
                                                $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_DISTRIBUTED);
                                                break;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (array_key_exists('rowspan', $cellContent)) {
                        $cell->setRowSpan($cellContent['rowspan']);
                    }
                    if (array_key_exists('width', $cellContent)) {
                        $cell->setWidth($cellContent['width']);
                    }
                    $border = $cell->getBorders();
                    if (array_key_exists('border', $cellContent)) {
                        if (array_key_exists('top', $cellContent['border']) && array_key_exists('width', $cellContent['border']['top'])) {
                            $border->getTop()->setLineWidth($cellContent['border']['top']['width'])
                                   ->setLineStyle(Border::LINE_SINGLE)
                                   ->setDashStyle(Border::DASH_SOLID);
                        } else {
                            $border->getTop()->setLineWidth(0)->setLineStyle(Border::LINE_NONE);
                        }
                        if (array_key_exists('right', $cellContent['border']) && array_key_exists('width', $cellContent['border']['right'])) {
                            $border->getRight()->setLineWidth($cellContent['border']['right']['width'])
                                   ->setLineStyle(Border::LINE_SINGLE)
                                   ->setDashStyle(Border::DASH_SOLID);
                        } else {
                            $border->getRight()->setLineWidth(0)->setLineStyle(Border::LINE_NONE);
                        }
                        if (array_key_exists('bottom', $cellContent['border']) && array_key_exists('width', $cellContent['border']['bottom'])) {
                            $border->getBottom()->setLineWidth($cellContent['border']['bottom']['width'])
                                   ->setLineStyle(Border::LINE_SINGLE)
                                   ->setDashStyle(Border::DASH_SOLID);
                        } else {
                            $border->getBottom()->setLineWidth(0)->setLineStyle(Border::LINE_NONE);
                        }
                        if (array_key_exists('left', $cellContent['border']) && array_key_exists('width', $cellContent['border']['left'])) {
                            $border->getLeft()->setLineWidth($cellContent['border']['left']['width'])
                                   ->setLineStyle(Border::LINE_SINGLE)
                                   ->setDashStyle(Border::DASH_SOLID);
                        } else {
                            $border->getLeft()->setLineWidth(0)->setLineStyle(Border::LINE_NONE);
                        }
                    } else {
                        $border->getTop()->setLineWidth(0)->setLineStyle(Border::LINE_NONE);
                        $border->getRight()->setLineWidth(0)->setLineStyle(Border::LINE_NONE);
                        $border->getBottom()->setLineWidth(0)->setLineStyle(Border::LINE_NONE);
                        $border->getLeft()->setLineWidth(0)->setLineStyle(Border::LINE_NONE);
                    }
                    $margins = $cell->getMargins();
                    if (array_key_exists('margin', $cellContent)) {
                    } else {
                        $margins->setMarginTop($this->defaultTableMarginTop);
                        $margins->setMarginRight($this->defaultTableMarginRight);
                        $margins->setMarginBottom($this->defaultTableMarginBottom);
                        $margins->setMarginLeft($this->defaultTableMarginLeft);
                    }
                }
            }
        }
        if (isset($shape)) {
            return $shape;
        } else {
            return null;
        }
    }

    public function getPowerPoint()
    {
        $this->setProperties();
        $objWriter = IOFactory::createWriter($this->ppt, 'PowerPoint2007');
        if (!is_null($this->slideMaster)) {
            $objWriter->setLayoutPack(new TemplateBased($this->slideMaster)); /* @phpstan-ignore-line */
        }

        switch ($this->target) {
            case 'file':
                $objWriter->save(str_replace('.php', '.pptx', $this->path.$this->filename));
                break;
            case 'browser':
                $GLOBALS['output_buffer'] = ob_get_clean();
                ob_start();
                $objWriter->save('php://output');
                $html = ob_get_clean();
                header("Content-Disposition: attachment; filename=\"".$this->filename."\";");
                header("Pragma: public");                                           // required
                header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");                   // Date in the past
                header('Cache-Control: no-store, no-cache, must-revalidate');       // HTTP/1.1
                header('Cache-Control: pre-check=0, post-check=0, max-age=0');      // HTTP/1.1
                header("Cache-Control: private", false);                            // required for certain browsers
                header("Content-Type: application/vnd.ms-powerpoint; charset=\"utf-8\"");
                header("Content-Transfer-Encoding: binary");
                header("Content-Type: application/octet-stream");

                print($html);
                break;
        }
    }

    public function addSlide()
    {
        if ($this->firstSlide) {
            $this->currentSlide = $this->ppt->getActiveSlide();
            $this->firstSlide   = false;
        } else {
            $this->currentSlide = $this->ppt->createSlide();
            $this->currentSlide->createPageNumberShape(); /* @phpstan-ignore-line */
        }
    }

    public function setSlideLayout($layout)
    {
        $this->currentSlide->setSlideLayout($layout);
    }
}
