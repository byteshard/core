<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Database\Struct;

use byteShard\Enum\LogLevel;
use JsonSerializable;

/**
 * Class Parameters
 * @package byteShard\Database\Struct
 */
class Parameters implements JsonSerializable
{
    public function __construct(
        public string $server = '',
        public ?int   $port = null,
        public string $database = '',
        public string $username = '',
        public string $password = '',
        public string $schema = '')
    {
    }

    public function __debugInfo(): array
    {
        if (defined('DISCLOSE_CREDENTIALS') && DISCLOSE_CREDENTIALS === true && defined('LOGLEVEL') && LOGLEVEL === LogLevel::DEBUG) {
            return get_object_vars($this);
        }
        $debug_info             = get_object_vars($this);
        $debug_info['password'] = $this->password === '' ? '' : 'CONFIDENTIAL';
        return $debug_info;
    }

    public function jsonSerialize(): array
    {
        return $this->__debugInfo();
    }
}
