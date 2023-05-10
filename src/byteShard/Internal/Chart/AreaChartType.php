<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

abstract class AreaChartType extends View
{
    use Color;
    use Alpha;
    use Padding;

    public function __construct(ChartType $view, string $value)
    {
        parent::__construct($view, $value);
    }

    public function getView(): array
    {
        return $this->getFilteredView(
            $this->getColor(),
            $this->getAlpha(),
            $this->getPadding()
        );
    }
}
