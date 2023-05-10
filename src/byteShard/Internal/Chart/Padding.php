<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

trait Padding
{
    private ?int $paddingTop;
    private ?int $paddingBottom;
    private ?int $paddingLeft;
    private ?int $paddingRight;

    /** @API */
    public function setPadding(?int $top = null, ?int $bottom = null, ?int $left = null, ?int $right = null): self
    {
        $this->paddingTop    = $top;
        $this->paddingBottom = $bottom;
        $this->paddingLeft   = $left;
        $this->paddingRight  = $right;
        return $this;
    }

    protected function getPadding(): array
    {
        $padding = [
            'top'    => $this->paddingTop ?? null,
            'bottom' => $this->paddingBottom ?? null,
            'left'   => $this->paddingLeft ?? null,
            'right'  => $this->paddingRight ?? null
        ];
        $padding = array_filter($padding, function ($value) {
            return $value !== null;
        });
        if (count($padding) === 0) {
            $padding = null;
        }
        return ['padding' => $padding];
    }
}
