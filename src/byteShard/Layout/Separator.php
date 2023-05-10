<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Layout;

class Separator
{
    private int $id;
    private int $width;

    public function __construct(int $id, int $width)
    {
        $this->id    = $id;
        $this->width = $width;
    }

    public function getSeparatorSize(): array
    {
        return [$this->id, $this->width];
    }
}
