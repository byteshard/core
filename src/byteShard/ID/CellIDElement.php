<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\ID;

class CellIDElement implements IDElementInterface
{
    private string $value;

    /**
     * @param string $value the fully qualified namespace of the cell content
     */
    public function __construct(string $value)
    {
        if (str_starts_with(strtolower($value), 'app\\cell\\')) {
            $this->value = trim(substr($value, 8), '\\');
        } else {
            $this->value = $value;
        }
    }

    public function getId(): string
    {
        return ID::CELLID;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getIdElement(): array
    {
        return [ID::CELLID => $this->value];
    }
}