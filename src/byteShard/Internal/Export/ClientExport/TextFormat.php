<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Export\ClientExport;

use PhpOffice\PhpPresentation\Style\Color;

class TextFormat
{
    private $bold         = false;
    private $fontName     = 'Helvetica';
    private $size         = 30;
    private $color        = '000000';
    private $defaultColor = true;

    public function __construct($size = null, $color = null, $name = null, $bold = false)
    {
        if (!is_null($color) && $color != '000000') {
            $this->color        = new Color($color);
            $this->defaultColor = false;
        }
        if (!is_null($size)) {
            $this->size = $size;
        }
        if (!is_null($name)) {
            $this->fontName = $name;
        }
        if ($bold) {
            $this->bold = $bold;
        }
    }

    public function applyFormat($textRunObject)
    {
        $font = $textRunObject->getFont();
        if (!$this->defaultColor) {
            $font->setColor($this->color);
        }
        if ($this->bold) {
            $font->setBold(true);
        }
        $font->setName($this->fontName);
        $font->setSize($this->size);
    }
}
