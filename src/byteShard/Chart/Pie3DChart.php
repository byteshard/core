<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Chart;

use byteShard\Internal\Chart\ChartType;
use byteShard\Internal\Chart\PieChartType;

/** @API */
class Pie3DChart extends PieChartType
{
    private float $cant;
    private int   $height;

    public function __construct(string $value)
    {
        parent::__construct(ChartType::PIE3D, $value);
    }

    /** @API */
    public function setCant(float $cant): self
    {
        $this->cant = $cant;
        return $this;
    }

    /** @API */
    public function setHeight(int $height): self
    {
        $this->height = $height;
        return $this;
    }

    public function getView(): array
    {
        return $this->getFilteredView(
            parent::getView(),
            ['height' => $this->height ?? null],
            ['cant' => $this->cant ?? null]
        );
    }
}
