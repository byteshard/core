<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\ID;

class PopupIDElement implements IDElementInterface
{
    private string $value;

    public function __construct(string $value)
    {
        if (str_starts_with(strtolower($value), 'app\\popup\\')) {
            $this->value = substr($value, 10);
        } else {
            $this->value = $value;
        }
    }

    public function getId(): string
    {
        return ID::TABID;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getIdElement(): array
    {
        return [ID::TABID => $this->value];
    }
}
