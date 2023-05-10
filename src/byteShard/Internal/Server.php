<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal;

/**
 * Class Server
 * @package byteShard\Internal
 */
class Server
{
    private static string $host     = '';
    private static string $protocol = '';

    /**
     * @return string
     */
    public static function getHost(): string
    {
        if (self::$host === '') {
            $host_keys = array('HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR');

            $host = '';
            foreach ($host_keys as $key) {
                if (isset($_SERVER[$key]) && !empty($_SERVER[$key])) {
                    $host = $_SERVER[$key];
                    break;
                }
            }

            // HTTP_X_FORWARDED_HOST might be a comma separated list
            if (str_contains($host, ',')) {
                $tmp  = explode(',', $host);
                $host = trim(end($tmp));
            }

            // Remove port number from host
            $host = preg_replace('/:\d+$/', '', $host);

            $port      = 443;
            $port_keys = array('HTTP_X_FORWARDED_PORT', 'SERVER_PORT');
            foreach ($port_keys as $key) {
                if (isset($_SERVER[$key]) && !empty($_SERVER[$key])) {
                    $port = (int)$_SERVER[$key];
                    break;
                }
            }
            if ($port === 443 || $port === 80) {
                $port = '';
            } else {
                $port = ':'.$port;
            }
            $host_parts = explode('/', $host);
            if (count($host_parts) > 1) {
                $host_name  = array_shift($host_parts);
                $context    = implode('/', $host_parts);
                self::$host = trim($host_name.$port.'/'.$context);
            } else {
                self::$host = trim($host.$port);
            }
        }
        return self::$host;
    }

    /**
     * returns the protocol of the request. This method will also evaluate headers used in a proxy
     * @return string
     */
    public static function getProtocol(): string
    {
        if (self::$protocol === '') {
            $https = false;
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                $https = true;
            } elseif ((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_HTTPS']) && $_SERVER['HTTP_X_FORWARDED_HTTPS'] === 'on')) {
                $https = true;
            }
            self::$protocol = (($https === true) ? 'https' : 'http');
        }
        return self::$protocol;
    }
}
