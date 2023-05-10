<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Chart\Legend;

class Value
{
    private string     $text;
    private string     $color;
    private MarkerType $markerType;
    private bool       $toggle;

    public function __construct(?string $text = null, ?string $color = null, ?MarkerType $markerType = null, ?bool $toggle = null)
    {
        if ($text !== null) {
            $this->text = $text;
        }
        if ($color !== null) {
            $this->color = $color;
        }
        if ($markerType !== null) {
            $this->markerType = $markerType;
        }
        if ($toggle !== null) {
            $this->toggle = $toggle;
        }
    }

    /** @API */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /** @API */
    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    /** @API */
    public function setMarkerType(MarkerType $markerType): self
    {
        $this->markerType = $markerType;
        return $this;
    }

    /** @API */
    public function setToggle(bool $toggle): self
    {
        $this->toggle = $toggle;
        return $this;
    }

    public function getValue(): array
    {
        $value = [
            'text'       => $this->text ?? null,
            'color'      => $this->color ?? null,
            'markerType' => $this->markerType?->value ?? null,
            'toggle'     => $this->toggle ?? null,
        ];
        return array_filter($value, function ($arrayValue) {
            return $arrayValue !== null;
        });
    }
}
