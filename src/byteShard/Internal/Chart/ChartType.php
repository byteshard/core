<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

enum ChartType: string
{
    // Bar charts
    // https://docs.dhtmlx.com/chart__dhxbar.html
    case BAR = 'bar';
    case STACKED_BAR = 'stackedBar';
    case HORIZONTAL_BAR = 'barH';
    case STACKED_HORIZONTAL_BAR = 'stackedBarH';

    // Line charts
    // https://docs.dhtmlx.com/chart__dhxgraphic.html
    case LINE = 'line';
    case SPLINE = 'spline';

    // Area charts
    // https://docs.dhtmlx.com/chart__dhxarea.html
    case AREA = 'area';
    case STACKED_AREA = 'stackedArea';

    // Radar charts
    // https://docs.dhtmlx.com/chart__dhxradar.html
    case RADAR = 'radar';

    // Scatter charts
    // https://docs.dhtmlx.com/chart__dhxscatter.html
    case SCATTER = 'scatter';

    // Pie charts
    // https://docs.dhtmlx.com/chart__dhxpie.html
    case PIE = 'pie';
    case PIE3D = 'pie3D';
    case DONUT = 'donut';
}