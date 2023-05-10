<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

trait Shadow
{
    private bool $shadow;

    /** @API */
    public function setShadow(bool $shadow = true): self
    {
        $this->shadow = $shadow;
        return $this;
    }

    /**
     * @return array<string,bool|null>
     */
    protected function getShadow(): array
    {
        return ['shadow' => $this->shadow ?? null];
    }
}
