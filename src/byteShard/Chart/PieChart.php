<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Chart;

use byteShard\Internal\Chart\ChartType;
use byteShard\Internal\Chart\LabelLines;
use byteShard\Internal\Chart\LabelOffset;
use byteShard\Internal\Chart\PieChartType;
use byteShard\Internal\Chart\Shadow;

/** @API */
class PieChart extends PieChartType
{
    use Shadow;
    use LabelLines;
    use LabelOffset;
    
    public function __construct(string $value)
    {
        parent::__construct(ChartType::PIE, $value);
    }

    public function getView(): array
    {
        return $this->getFilteredView(
            parent::getView(),
            $this->getShadow(),
            $this->getLabelLines(),
            $this->getLabelOffset(),
        );
    }
}
