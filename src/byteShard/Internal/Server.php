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
    private static int    $port     = 0;

    /**
     * @return string
     */
    public static function getHost(): string
    {
        if (self::$host === '') {
            $hostKeys = array('HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR');

            $host = '';
            foreach ($hostKeys as $key) {
                if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
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

            $port = self::getPort();
            if ($port === 443 || $port === 80) {
                $port = '';
            } else {
                $port = ':'.$port;
            }
            $hostParts = explode('/', $host);
            if (count($hostParts) > 1) {
                $hostName   = array_shift($hostParts);
                $context    = implode('/', $hostParts);
                self::$host = trim($hostName.$port.'/'.$context);
            } else {
                self::$host = trim($host.$port);
            }
        }
        return self::$host;
    }

    public static function getPort(): int
    {
        if (self::$port === 0) {
            $port     = 443;
            $portKeys = ['HTTP_X_FORWARDED_PORT', 'SERVER_PORT'];
            foreach ($portKeys as $key) {
                if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
                    $port = (int)$_SERVER[$key];
                    break;
                }
            }
            self::$port = $port;
        }
        return self::$port;
    }

    /**
     * returns the protocol of the request. This method will also evaluate headers used in a proxy
     * @return string
     */
    public static function getProtocol(): string
    {
        if (self::$protocol === '') {
            $https = false;
            if (
                (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] === 'on') ||
                (array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                (array_key_exists('HTTP_X_FORWARDED_SSL', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
                (array_key_exists('HTTP_X_FORWARDED_HTTPS', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_HTTPS'] === 'on')
            ) {
                $https = true;
            }
            self::$protocol = (($https === true) ? 'https' : 'http');
        }
        return self::$protocol;
    }

    /**
     * the base url should be the url with protocol and the context the app is running under with an optional port
     * e.g. https://byteshard.bespin.biz<:8443>/app
     * It always ends without a slash
     * @return string
     */
    public static function getBaseUrl(): string
    {
        if (class_exists('\\config')) {
            $config = new \config();
            if ($config instanceof Config) {
                $baseUrl = $config->getUrl().$config->getUrlContext();
                if (str_ends_with($baseUrl, '/')) {
                    $baseUrl = substr($baseUrl, 0, -1);
                }
                return $baseUrl;
            }
        }
        Debug::warning('No app config defined. Please define "protected ?string $url" in the config otherwise this will be derived from unsafe headers');
        return self::getProtocol().self::getHost();
    }
}
