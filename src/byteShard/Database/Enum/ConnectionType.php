<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Database\Enum;

/**
 * Class ConnectionType
 * @package byteShard\Database\Enum
 */
enum ConnectionType
{
    case READ;
    case WRITE;
    case LOGIN;
    case ADMIN;
}
