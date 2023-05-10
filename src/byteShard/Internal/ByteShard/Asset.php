<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\ByteShard;

abstract class Asset
{

    protected function clean(string $value): string
    {
        return trim($value, " \t\n\r\0\x0B/\\");
    }
}