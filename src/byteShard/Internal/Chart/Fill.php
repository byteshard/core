<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

trait Fill
{
    private string $fill;

    /** @API */
    public function setFill(string $fill): self
    {
        $this->fill = $fill;
        return $this;
    }

    protected function getFill(): array
    {
        return ['fill' => $this->fill ?? null];
    }
}
