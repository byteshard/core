<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

trait LabelLines
{
    private bool $labelLines;

    /** @API */
    public function setLabelLines(bool $labelLines): self
    {
        $this->labelLines = $labelLines;
        return $this;
    }

    /**
     * @return array<string,bool|null>
     */
    protected function getLabelLines(): array
    {
        return ['labelLines' => $this->labelLines ?? null];
    }
}
