<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Export\ClientExport;

class ShapeFormat
{

    public function __construct(private string $hAlign = 'l', private string $vAlign = 't')
    {
    }

    public function applyFormat(&$shapeObject)
    {
        if (!is_null($this->hAlign) || !is_null($this->vAlign)) {
            $align = $shapeObject->getActiveParagraph()->getAlignment();
            if (!is_null($this->hAlign)) {
                $align->setHorizontal($this->hAlign);
            }
            if (!is_null($this->vAlign)) {
                $align->setVertical($this->vAlign);
            }
        }
    }
}
