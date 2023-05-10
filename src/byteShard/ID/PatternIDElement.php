<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\ID;

class PatternIDElement implements IDElementInterface
{
    /**
     * @param string $value the fully qualified namespace of the cell content
     */
    public function __construct(private readonly string $value)
    {
    }

    public function getId(): string
    {
        return ID::PATTERNID;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getIdElement(): array
    {
        return [ID::PATTERNID => $this->value];
    }
}