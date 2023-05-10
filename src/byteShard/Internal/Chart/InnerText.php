<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

trait InnerText
{
    private string $innerText;

    /** @API */
    public function setInnerText(string $innerText): self
    {
        $this->innerText = $innerText;
        return $this;
    }

    /** @return array<string,null|string> */
    protected function getInnerText(): array
    {
        return ['pieInnerText' => $this->innerText ?? null];
    }
}
