<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\ID;

interface IDElementInterface
{
    public function getId(): string;

    public function getIdElement(): array;

    public function getValue(): string|int;
}