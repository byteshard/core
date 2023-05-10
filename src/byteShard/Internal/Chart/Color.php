<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

trait Color
{
    private string $color;

    /** @API */
    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    /** @return array<string,null|string> */
    protected function getColor(): array
    {
        return ['color' => $this->color ?? null];
    }
}
