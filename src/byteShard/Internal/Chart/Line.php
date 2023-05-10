<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

use byteShard\Chart\Property\Line as LineObject;

trait Line
{
    private LineObject $line;

    /** @API */
    public function setLine(LineObject $line): self
    {
        $this->line = $line;
        return $this;
    }

    protected function getLine(): array
    {
        if (isset($this->line)) {
            return $this->line->getLine() ?? ['line' => null];
        }
        return ['line' => null];
    }
}
