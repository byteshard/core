<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Chart\Legend;

class Marker
{
    private MarkerType $type;
    private int        $radius;
    private int        $width;
    private int        $height;

    public function __construct(?MarkerType $type = null, ?int $radius = null, ?int $width = null, ?int $height = null)
    {
        if ($type !== null) {
            $this->type = $type;
        }
        if ($radius !== null) {
            $this->radius = $radius;
        }
        if ($width !== null) {
            $this->width = $width;
        }
        if ($height !== null) {
            $this->height = $height;
        }
    }

    /** @API */
    public function setType(MarkerType $type): self
    {
        $this->type = $type;
        return $this;
    }

    /** @API */
    public function setRadius(int $radius): self
    {
        $this->radius = $radius;
        return $this;
    }

    /** @API */
    public function setWidth(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    /** @API */
    public function setHeight(int $height): self
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return array<string,null|array<string,int|string>>
     * @API
     */
    public function getMarker(): array
    {
        $marker = [
            'type'   => $this->type->value ?? null,
            'radius' => $this->radius ?? null,
            'width'  => $this->width ?? null,
            'height' => $this->height ?? null,
        ];
        $marker = array_filter($marker, function ($value) {
            return $value !== null;
        });
        if (count($marker) === 0) {
            $marker = null;
        }
        return ['marker' => $marker];
    }
}