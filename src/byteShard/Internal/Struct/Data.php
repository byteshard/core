<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Struct;

class Data
{
    public mixed   $value;
    public ?string $type;

    /**
     * Data constructor.
     * @param mixed $value
     * @param string|null $type
     */
    public function __construct(mixed $value, ?string $type)
    {
        $this->value = $value;
        $this->type  = $type;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if ($this->value !== null) {
            return (string)$this->value;
        }
        return '';
    }
}
