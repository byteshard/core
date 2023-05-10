<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Export\ClientExport;

class Rect
{
    public $height;
    public $width;
    public $offsetX;
    public $offsetY;

    public function __construct($height, $width, $x, $y)
    {
        $this->height  = $this->convert($height);
        $this->width   = $this->convert($width);
        $this->offsetX = $this->convert($x);
        $this->offsetY = $this->convert($y);
    }

    public static function convert($value)
    {
        return $value;
        //return $value / ((264 + (2 / 3)) / 10000);
    }
}