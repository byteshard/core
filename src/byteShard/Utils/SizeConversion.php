<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Utils;

class SizeConversion
{
    /**
     * @API
     * @param float $px
     * @return float
     */
    public static function pxToPt(float $px): float
    {
        return $px * 0.75;
    }

    /**
     * @API
     * @param float $pt
     * @param int $precision
     * @return float
     */
    public static function ptToPx(float $pt, int $precision = 2): float
    {
        return round($pt / 0.75, $precision);
    }

    /**
     * @API
     * @param float $mm
     * @param int $precision
     * @return float
     */
    public static function mmToPx(float $mm, int $precision = 0): float
    {
        return round($mm * 480 / 127, $precision);
    }

    /**
     * @API
     * @param float $px
     * @param int $precision
     * @return float
     */
    public static function pxToMm(float $px, int $precision = 2): float
    {
        return round($px / 480 * 127, $precision);
    }
}
