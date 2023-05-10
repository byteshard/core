<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Chart;

use byteShard\Internal\Chart\ChartType;
use byteShard\Internal\Chart\BarChartType;

/** @API */
class BarChart extends BarChartType
{
    public function __construct(string $value)
    {
        parent::__construct(ChartType::BAR, $value);
    }
}
