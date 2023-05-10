<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Chart\Property;

class Line
{
    private string $lineColor;
    private int    $lineWidth;

    public function __construct(?string $lineColor = null, ?int $lineWidth = null)
    {
        if ($lineColor !== null) {
            $this->lineColor = $lineColor;
        }
        if ($lineWidth !== null) {
            $this->lineWidth = $lineWidth;
        }
    }

    /** @API */
    public function setLineColor(string $color): self
    {
        $this->lineColor = $color;
        return $this;
    }

    /** @API */
    public function setLineWidth(int $width): self
    {
        $this->lineWidth = $width;
        return $this;
    }

    public function getLine(): array
    {
        $line = [
            'width' => $this->lineWidth ?? null,
            'color' => $this->lineColor ?? null,
        ];
        $line = array_filter($line, function ($value) {
            return $value !== null;
        });
        if (count($line) === 0) {
            $line = null;
        }
        return ['line' => $line];
    }
}
