<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

abstract class PieChartType extends View
{
    use Color;
    use Radius;
    use Label;
    use InnerText;
    use Tooltip;

    private bool $gradient = false;
    private int  $positionX;
    private int  $positionY;

    public function __construct(ChartType $view, string $value)
    {
        parent::__construct($view, $value);
    }

    /** @API */
    public function setGradient(bool $gradient = true): self
    {
        $this->gradient = $gradient;
        return $this;
    }

    /** @API */
    public function setPosition(int $x, int $y): self
    {
        $this->positionX = $x;
        $this->positionY = $y;
        return $this;
    }

    /** @return array<string,bool|null> */
    private function getGradient(): array
    {
        return ['gradient' => $this->gradient === true ? true : null];
    }

    /** @return array<string,int|null> */
    private function getPosition(): array
    {
        return [
            'x' => $this->positionX ?? null,
            'y' => $this->positionY ?? null
        ];
    }

    /** @return array<string,bool|int|null|string> */
    public function getView(): array
    {
        return $this->getFilteredView(
            $this->getColor(),
            $this->getRadius(),
            $this->getGradient(),
            $this->getPosition(),
            $this->getLabel(),
            $this->getInnerText(),
            $this->getTooltip()
        );
    }
}
