<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Chart;

use byteShard\Internal\Chart\AreaChartType;
use byteShard\Internal\Chart\ChartType;

/** @API */
class StackedAreaChart extends AreaChartType
{
    public function __construct(string $value)
    {
        parent::__construct(ChartType::STACKED_AREA, $value);
    }
}
