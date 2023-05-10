<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

trait Tooltip
{
    private string $tooltip;

    /** @API */
    public function setTooltip(string $tooltip): self
    {
        $this->tooltip = $tooltip;
        return $this;
    }

    /** @return array<string,null|string> */
    protected function getTooltip(): array
    {
        return ['tooltip' => $this->tooltip ?? null];
    }
}
