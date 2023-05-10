<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Cell;

/**
 * Class Storage
 * @package byteShard\Internal\Cell
 */
class Storage
{
    private mixed $defaultValue;
    private mixed $value = null;

    public function __construct(mixed $defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    public function getValue(): mixed
    {
        return $this->value !== null ? $this->value : $this->defaultValue;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }
}
