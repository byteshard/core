<?php

namespace byteShard\Internal\Deeplink;

use byteShard\ID\ID;
use byteShard\ID\TabIDElement;
use byteShard\Session;

class Deeplink
{

    private const COOKIE = 'deeplink';

    public static function checkReferrer(): void
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referrer      = $_SERVER['HTTP_REFERER'];
            $urlComponents = parse_url($referrer);

            if (isset($urlComponents['query'])) {
                parse_str($urlComponents['query'], $params);
                if (!empty($params)) {
                    self::setCookie($params);
                }
            }
        }
    }

    private static function sanitizeParameters(array $params)
    {
        $result = [];
        foreach ($params as $key => $value) {
            match ($key) {
                // list of allowed keys
                // Replace all non classname characters and reduce multiple backslashes to one
                'tab'   => $result[$key] = preg_replace('/\\\\+/', '\\', preg_replace('/[^a-zA-Z0-9_\\\\]/', '', $value)),
                default => null
            };
        }
        return $result;
    }

    public static function selectTab(): void
    {
        $getParams = !empty($_GET) ? $_GET : self::getCookie();
        $getParams = self::sanitizeParameters($getParams);
        if (is_array($getParams) && !empty($getParams)) {
            if (isset($getParams['tab'])) {
                Session::setSelectedTab(ID::factory(new TabIDElement($getParams['tab'])));
                unset($getParams['tab']);
                self::setCookie($getParams);
            }
        }
    }

    public static function getCookie(): ?array
    {
        if (array_key_exists(self::COOKIE, $_COOKIE)) {
            return json_decode($_COOKIE[self::COOKIE], true);
        }
        return [];
    }

    public static function cleanupCookie(): void
    {
        setcookie(self::COOKIE, '', [
            'expires'  => time() - 3600,
            'secure'   => true,
            'httponly' => true,
            'path'     => '/',
            'sameSite' => 'Strict'
        ]);
        unset($_COOKIE[self::COOKIE]);
    }

    public static function setCookie(array $params): void
    {
        $params = self::sanitizeParameters($params);
        if (empty($params)) {
            self::cleanupCookie();
        } else {
            setcookie(self::COOKIE, json_encode($params), [
                'expires'  => 0,
                'secure'   => true,
                'httponly' => true,
                'path'     => '/',
                'sameSite' => 'Strict'
            ]);
        }
    }
}