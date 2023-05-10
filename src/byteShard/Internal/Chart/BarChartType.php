<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

use byteShard\Chart\Property\ConditionalColor;
use byteShard\Chart\Property\Gradient;

abstract class BarChartType extends View
{
    use Radius;
    use Alpha;
    use Color;
    use Padding;
    use Tooltip;

    /** @var ConditionalColor[] */
    private array    $conditions;
    private bool     $border;
    private Gradient $gradient;
    private int      $width;

    public function __construct(ChartType $view, string $value)
    {
        parent::__construct($view, $value);
    }

    /** enables/disables bar borders
     * @API
     */
    public function setBorder(bool $border): self
    {
        $this->border = $border;
        return $this;
    }

    /** the bars gradient
     * @API
     */
    public function setGradient(Gradient $gradient): self
    {
        $this->gradient = $gradient;
        return $this;
    }

    /** the width of bars */
    public function setWidth(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @API
     */
    public function setConditionalColor(ConditionalColor ...$conditions): self
    {
        foreach ($conditions as $condition) {
            $this->conditions[] = $condition;
        }
        return $this;
    }

    private function getGradientCondition(): array
    {
        $result = [];
        foreach ($this->conditions as $condition) {
            $result = array_merge($result, $condition->getConditionalColor());
        }
        return $result;
    }

    public function getView(): array
    {
        $view = [];
        if (isset($this->conditions)) {
            $view['gradientCondition'] = $this->getGradientCondition();
        } else if (isset($this->gradient)) {
            $view['gradient'] = $this->gradient->value;
        }
        return $this->getFilteredView(
            $this->getColor(),
            $this->getAlpha(),
            $this->getRadius(),
            $this->getPadding(),
            $this->getTooltip(),
            ['width' => $this->width ?? null],
            ['border' => $this->border ?? null],
            $view
        );
    }
}
