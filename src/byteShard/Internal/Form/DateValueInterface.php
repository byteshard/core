<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use DateTime;

interface DateValueInterface
{
    public function setValue(null|string|DateTime $value): static;
    public function getValue(string $format = ''): string;
}