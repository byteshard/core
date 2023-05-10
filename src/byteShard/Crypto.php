<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard;

class Crypto
{
    public static function randomBytes(int $bytes): string
    {
        $attempts = 0;
        do {
            try {
                return random_bytes($bytes);
            } catch (\Exception $e) {
                $attempts++;
                continue;
            }
        } while ($attempts < 10);

        $buffer = '';
        $failed = false;
        if (@is_readable('/dev/urandom')) {
            $fileHandle = fopen('/dev/urandom', 'rb');
            if ($fileHandle !== false) {
                $stream = stream_set_read_buffer($fileHandle, 0);
                if ($stream === 0) {
                    $remaining = $bytes;
                    do {
                        $read = fread($fileHandle, $remaining);
                        if ($read === false) {
                            // We cannot safely read from urandom.
                            $failed = true;
                            break;
                        }
                        // Decrease the number of bytes returned from remaining
                        $remaining -= strlen($read);
                        $buffer    .= $read;
                    } while ($remaining > 0);
                    if ($failed === false) {
                        return $buffer;
                    }
                }
            }
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            $secure = true;
            $random = openssl_random_pseudo_bytes($bytes, $secure);
            if ($random !== false && $secure === true) {
                return $random;
            }
        }
        throw new Exception('Failed to create randomness', 107050000);
    }
}
