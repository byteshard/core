<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

abstract class LineChartType extends View
{
    use Padding;
    use Line;
    use Item;
    use Tooltip;

    public function __construct(ChartType $view, string $value)
    {
        parent::__construct($view, $value);
    }

    public function getView(): array
    {
        return $this->getFilteredView(
            $this->getPadding(),
            $this->getItem(),
            $this->getLine(),
            $this->getTooltip()
        );
    }
}
