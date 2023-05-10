<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Chart\Property;

enum ItemType: string
{
    case ROUND = 'r';
    case SQUARE = 's';
    case DIAMOND = 'd';
}
