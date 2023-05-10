<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard;

use DateTimeZone;

class Settings
{
    // appDefined acts as a cache against repetitive class_exists('\App\Settings')
    private static bool           $appDefined = false;
    protected static string       $idSuffix   = '_ID';
    protected static DateTimeZone $serverTimeZone;

    public static function getDateFormat(string $objectType): string
    {
        if (self::$appDefined === true && static::class !== '\App\Settings' && method_exists('\App\Settings', __FUNCTION__) || static::class !== '\App\Settings' && (self::$appDefined === false && (self::$appDefined = class_exists('\App\Settings')) === true) && method_exists('\App\Settings', __FUNCTION__)) {
            return \App\Settings::getDateFormat($objectType); /** @phpstan-ignore-line */
        }
        return 'Y-m-d H:i:s';
    }

    public static function getServerTimeZone(): DateTimeZone
    {
        if (self::$appDefined === true && static::class !== '\App\Settings' && method_exists('\App\Settings', __FUNCTION__) || static::class !== '\App\Settings' && (self::$appDefined === false && (self::$appDefined = class_exists('\App\Settings')) === true) && method_exists('\App\Settings', __FUNCTION__)) {
            return \App\Settings::getServerTimeZone(); /** @phpstan-ignore-line */
        }
        if (!isset(self::$serverTimeZone)) {
            // assign to static parameter to safe cpu time if getServerTimeZones is called many times
            self::$serverTimeZone = new DateTimeZone('UTC');
        }
        return self::$serverTimeZone;
    }

    /**
     * The function will return suffix _id
     * @return string
     */
    public static function getIDSuffix(): string
    {
        if (self::$appDefined === true && static::class !== '\App\Settings' && method_exists('\App\Settings', __FUNCTION__) || static::class !== '\App\Settings' && (self::$appDefined === false && (self::$appDefined = class_exists('\App\Settings')) === true) && method_exists('\App\Settings', __FUNCTION__)) {
            return \App\Settings::getIDSuffix(); /** @phpstan-ignore-line */
        }
        return static::$idSuffix;
    }
    
    public static function logTabChangeAndPopup(): bool
    {
        if (self::$appDefined === true && static::class !== '\App\Settings' && method_exists('\App\Settings', __FUNCTION__) || static::class !== '\App\Settings' && (self::$appDefined === false && (self::$appDefined = class_exists('\App\Settings')) === true) && method_exists('\App\Settings', __FUNCTION__)) {
            return \App\Settings::logTabChangeAndPopup(); /** @phpstan-ignore-line */
        }
        return true;
    }
}