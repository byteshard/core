<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Chart;

use byteShard\Chart\Axis\Axis;
use byteShard\Chart\Legend\Legend;
use byteShard\Chart\Legend\Marker;
use byteShard\Chart\Legend\MarkerType;
use byteShard\Chart\Legend\Value;
use byteShard\Chart\Property\ConditionalColor;
use byteShard\Chart\Property\Item;
use byteShard\Chart\Property\ItemType;
use byteShard\Chart\Property\Line;

/** @API */
class ChartFactory
{
    /** @API */
    public static function areaChart(string $value): AreaChart
    {
        return new AreaChart($value);
    }

    /** @API */
    public static function barChart(string $value): BarChart
    {
        return new BarChart($value);
    }

    /** @API */
    public static function donutChart(string $value): DonutChart
    {
        return new DonutChart($value);
    }

    /** @API */
    public static function horizontalBarChart(string $value): HorizontalBarChart
    {
        return new HorizontalBarChart($value);
    }

    /** @API */
    public static function lineChart(string $value): LineChart
    {
        return new LineChart($value);
    }

    /** @API */
    public static function pie3DChart(string $value): Pie3DChart
    {
        return new Pie3DChart($value);
    }

    /** @API */
    public static function pieChart(string $value): PieChart
    {
        return new PieChart($value);
    }

    /** @API */
    public static function radarChart(string $value): RadarChart
    {
        return new RadarChart($value);
    }

    /** @API */
    public static function scatterChart(string $xValue, string $yValue): ScatterChart
    {
        return new ScatterChart($xValue, $yValue);
    }

    /** @API */
    public static function splineChart(string $value): SplineChart
    {
        return new SplineChart($value);
    }

    /** @API */
    public static function stackedAreaChart(string $value): StackedAreaChart
    {
        return new StackedAreaChart($value);
    }

    /** @API */
    public static function stackedBarChart(string $value): StackedBarChart
    {
        return new StackedBarChart($value);
    }

    /** @API */
    public static function stackedHorizontalBarChart(string $value): StackedHorizontalBarChart
    {
        return new StackedHorizontalBarChart($value);
    }

    /** @API */
    public static function legend(): Legend
    {
        return new Legend();
    }

    /** @API */
    public static function axis(?string $title = null, ?string $color = null, ?string $template = null, ?string $lineColor = null, ?bool $lines = null, ?int $start = null, ?int $step = null, ?int $end = null): Axis
    {
        return new Axis($title, $color, $template, $lineColor, $lines, $start, $step, $end);
    }

    /** @API */
    public static function marker(?MarkerType $type = null, ?int $radius = null, ?int $width = null, ?int $height = null): Marker
    {
        return new Marker($type, $radius, $width, $height);
    }

    /** @API */
    public static function value(?string $text = null, ?string $color = null, ?MarkerType $markerType = null, ?bool $toggle = null): Value
    {
        return new Value($text, $color, $markerType, $toggle);
    }

    /** @API */
    public static function conditionalColor(float $value, string $color): ConditionalColor
    {
        return new ConditionalColor($value, $color);
    }

    /** @API */
    public static function item(?string $borderColor = null, ?int $borderWidth = null, ?string $color = null, ?int $radius = null, ?ItemType $type = null): Item
    {
        return new Item($borderColor, $borderWidth, $color, $radius, $type);
    }

    /** @API */
    public static function line(?string $lineColor = null, ?int $lineWidth = null): Line
    {
        return new Line($lineColor, $lineWidth);
    }
}
