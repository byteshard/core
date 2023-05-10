<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

abstract class RadarChartType extends View
{
    use Alpha;
    use Line;
    use Item;
    use Padding;
    use Tooltip;
    use Fill;

    private bool $disableItems;
    private bool $disableLines;

    public function __construct(ChartType $view, string $value)
    {
        parent::__construct($view, $value);
    }

    /** @API */
    public function disableItems(): self
    {
        $this->disableItems = true;
        return $this;
    }

    /** @API */
    public function disableLines(): self
    {
        $this->disableLines = true;
        return $this;
    }

    public function getView(): array
    {
        return $this->getFilteredView(
            $this->getAlpha(),
            $this->getLine(),
            $this->getItem(),
            $this->getTooltip(),
            $this->getFill(),
            ['disableLines' => $this->disableLines ?? null],
            ['disableItems' => $this->disableItems ?? null],
        );
    }
}
