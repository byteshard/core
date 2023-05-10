<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

trait Radius
{
    private string $radius;

    /** @API */
    public function setRadius(string $radius): self
    {
        $this->radius = $radius;
        return $this;
    }

    /** @return array<string,null|string> */
    protected function getRadius(): array
    {
        return ['radius' => $this->radius ?? null];
    }
}
