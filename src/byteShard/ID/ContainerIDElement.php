<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\ID;

class ContainerIDElement implements IDElementInterface
{
    private string $value;

    public function __construct(string $value)
    {
        if (str_starts_with(strtolower($value), 'app\\container\\')) {
            $this->value = substr($value, 14);
        } else {
            $this->value = $value;
        }
    }

    public function getId(): string
    {
        return ID::CONTAINERID;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getIdElement(): array
    {
        return [ID::CONTAINERID => $this->value];
    }
}
