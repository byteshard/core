<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal;

/**
 * Class Sanitizer
 * @package byteShard\Internal
 */
class Sanitizer
{
    private static ?bool $utf8decode = null;

    final public static function sanitize(mixed $input, ?string $type = null): string|array|null|object|bool
    {
        if (self::$utf8decode === null) {
            self::$utf8decode = true;
            if (class_exists('\\config')) {
                /** @noinspection PhpUndefinedClassInspection */
                $config = new \config();
                if ($config instanceof Config) {
                    self::$utf8decode = $config->useDecodeUtf8();
                }
            }
        }

        switch (true) {
            case is_object($input):
            case is_array($input):
                $result = [];
                foreach ($input as $key => $val) {
                    $result[$key] = self::sanitize($val);
                }
                return is_object($input) ? (object)$result : $result;
            case is_null($input):
            case is_bool($input):
                return $input;
            default:
                if ($type === 'username') {
                    return self::sanitize_username($input);
                }
                if (self::$utf8decode === true) {
                    return self::sanitizeAndDecode($input);
                }
                return htmlspecialchars($input, 16, 'UTF-8', false);
        }
    }

    private static function sanitizeAndDecode(string $string): string
    {
        if (!preg_match("//u", $string)) {
            return mb_convert_encoding(htmlspecialchars($string, 16, 'UTF-8', false),  'ISO-8859-1', 'UTF-8');
        }
        return htmlspecialchars($string, 16, 'UTF-8', false);
    }

    private static function sanitize_username(string $username): string
    {
        if (!preg_match('/^[a-zA-Z0-9!#$%&@*+.\-\/=?^_{|}~]]+$/', $username)) {
            $username = '';
        }
        return $username;
    }
}
