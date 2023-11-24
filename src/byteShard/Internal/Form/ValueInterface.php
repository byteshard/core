<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

interface ValueInterface
{
    public function setValue(null|string|int|float $stringOrInt): static;
    public function getValue(): null|string|int|float;
}