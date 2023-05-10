<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

trait Label
{
    private string $label;

    /** @API */
    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /** @return array<string,null|string> */
    protected function getLabel(): array
    {
        return ['label' => $this->label ?? null];
    }
}
