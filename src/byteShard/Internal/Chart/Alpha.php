<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

trait Alpha
{
    private float $alpha;

    /** @API */
    public function setAlpha(float $alpha): self
    {
        $this->alpha = $alpha;
        return $this;
    }

    protected function getAlpha(): array
    {
        return ['alpha' => $this->alpha ?? null];
    }
}