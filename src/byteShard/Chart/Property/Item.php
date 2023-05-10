<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Chart\Property;

class Item
{
    private string   $borderColor;
    private int      $borderWidth;
    private string   $color;
    private int      $radius;
    private ItemType $type;

    public function __construct(?string $borderColor = null, ?int $borderWidth = null, ?string $color = null, ?int $radius = null, ?ItemType $type = null)
    {
        if ($borderColor !== null) {
            $this->borderColor = $borderColor;
        }
        if ($borderWidth !== null) {
            $this->borderWidth = $borderWidth;
        }
        if ($color !== null) {
            $this->color = $color;
        }
        if ($radius !== null) {
            $this->radius = $radius;
        }
        if ($type !== null) {
            $this->type = $type;
        }
    }

    /** @API */
    public function setBorderColor(string $borderColor): self
    {
        $this->borderColor = $borderColor;
        return $this;
    }

    /** @API */
    public function setBorderWidth(int $borderWidth): self
    {
        $this->borderWidth = $borderWidth;
        return $this;
    }

    /** @API */
    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    /** @API */
    public function setRadius(int $radius): self
    {
        $this->radius = $radius;
        return $this;
    }

    /** @API */
    public function setType(ItemType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getItem(): array
    {
        $items = [
            'borderColor' => $this->borderColor ?? null,
            'borderWidth' => $this->borderWidth ?? null,
            'color'       => $this->color ?? null,
            'type'        => $this->type?->value ?? null,
            'radius'      => $this->radius ?? null
        ];
        $items = array_filter($items, function ($value) {
            return $value !== null;
        });
        if (count($items) === 0) {
            $items = null;
        }
        return ['item' => $items];
    }
}
