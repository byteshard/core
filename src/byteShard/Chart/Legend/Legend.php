<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Chart\Legend;

class Legend
{
    /** @var Value[] */
    private array           $values = [];
    private HorizontalAlign $horizontalAlign;
    private VerticalAlign   $verticalAlign;
    private Layout          $layout;
    private Marker          $marker;
    private int             $width;
    private int             $padding;
    private int             $margin;

    /**
     * @return array<string,int|string>
     */
    public function getLegend(): array
    {
        $result['width']   = $this->width ?? null;
        $result['padding'] = $this->padding ?? null;
        $result['margin']  = $this->margin ?? null;
        $result['layout']  = $this->layout->value ?? null;
        $result['align']   = $this->horizontalAlign->value ?? null;
        $result['valign']  = $this->verticalAlign->value ?? null;
        if (isset($this->marker)) {
            $result = array_merge($result, $this->marker->getMarker());
        }
        foreach ($this->values as $value) {
            $result['values'][] = $value->getValue();
        }
        return array_filter($result, function ($value) {
            return $value !== null;
        });
    }

    /** @API */
    public function addValue(Value ...$values): self
    {
        foreach ($values as $value) {
            $this->values[] = $value;
        }
        return $this;
    }

    /** @API */
    public function setMarker(Marker $marker): self
    {
        $this->marker = $marker;
        return $this;
    }

    /** @API */
    public function setWidth(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    /** @API */
    public function setPadding(int $padding): self
    {
        $this->padding = $padding;
        return $this;
    }

    /** @API */
    public function setMargin(int $margin): self
    {
        $this->margin = $margin;
        return $this;
    }

    /** @API */
    public function setAlign(HorizontalAlign $horizontalAlign): self
    {
        $this->horizontalAlign = $horizontalAlign;
        return $this;
    }

    /** @API */
    public function setVerticalAlign(VerticalAlign $verticalAlign): self
    {
        $this->verticalAlign = $verticalAlign;
        return $this;
    }

    /** @API */
    public function setLayout(Layout $layout): self
    {
        $this->layout = $layout;
        return $this;
    }
}
