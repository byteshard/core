<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

trait LabelOffset
{
    private string $labelOffset;

    /** @API */
    public function setLabelOffset(string $labelOffset): self
    {
        $this->labelOffset = $labelOffset;
        return $this;
    }

    /**
     * @return array<string,null|string>
     */
    protected function getLabelOffset(): array
    {
        return ['labelOffset' => $this->labelOffset ?? null];
    }
}
