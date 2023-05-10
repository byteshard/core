<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

abstract class ScatterChartType extends View
{
    use Tooltip;
    use Item;

    private string $xValue;
    private string $yValue;

    public function __construct(ChartType $view, string $xValue, string $yValue)
    {
        parent::__construct($view, null);
        $this->xValue = $xValue;
        $this->yValue = $yValue;
    }

    public function getView(): array
    {
        return $this->getFilteredView(
            [
                'xValue' => $this->xValue,
                'yValue' => $this->yValue,
            ],
            $this->getTooltip(),
            $this->getItem()
        );
    }
}
