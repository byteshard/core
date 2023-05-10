<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Utils\StringDimension;

class Line
{
    public function __construct(private string $text, private int $width, private readonly int $spaceWidth)
    {

    }

    public function addWord(string $word, int $wordWidth): void
    {
        if ($this->text !== '') {
            $this->text .= ' ';
        }
        $this->text  .= $word;
        $this->width += $wordWidth + $this->spaceWidth;
    }

    public function getLine(): string
    {
        return $this->text;
    }

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function getWidth(): int
    {
        return $this->width;
    }
}
