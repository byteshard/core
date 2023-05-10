<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\ID;

class IDElement implements IDElementInterface
{
    public function __construct(private readonly string $id, private readonly string|int $value)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): string|int
    {
        return $this->value;
    }

    public function getIdElement(): array
    {
        return [$this->id => $this->value];
    }
}