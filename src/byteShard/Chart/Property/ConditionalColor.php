<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Chart\Property;

class ConditionalColor
{
    public function __construct(private readonly float $value, private readonly string $color)
    {
    }

    public function getConditionalColor(): array
    {
        return [(string)$this->value => $this->color];
    }
}
