<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Chart\Axis;

class Axis
{
    private string    $title;
    private string    $color;
    private int       $start;
    private int       $end;
    private int       $step;
    private int       $decimalDigits;
    private string    $template;
    private bool      $lines;
    private string    $lineColor;
    private LineShape $lineShape;

    public function __construct(?string $title = null, ?string $color = null, ?string $template = null, ?string $lineColor = null, ?bool $lines = null, ?int $start = null, ?int $step = null, ?int $end = null)
    {
        if ($title !== null) {
            $this->title = $title;
        }
        if ($color !== null) {
            $this->color = $color;
        }
        if ($template !== null) {
            $this->template = $template;
        }
        if ($lineColor !== null) {
            $this->lineColor = $lineColor;
        }
        if ($lines !== null) {
            $this->lines = $lines;
        }
        if ($start !== null) {
            $this->start = $start;
        }
        if ($step !== null) {
            $this->step = $step;
        }
        if ($end !== null) {
            $this->end = $end;
        }
    }

    /**
     * @return array<string,int|bool|string>
     */
    public function getAxis(): array
    {
        $axis['title']         = $this->title ?? null;
        $axis['color']         = $this->color ?? null;
        $axis['start']         = $this->start ?? null;
        $axis['end']           = $this->end ?? null;
        $axis['step']          = $this->step ?? null;
        $axis['decimalDigits'] = $this->decimalDigits ?? null;
        $axis['template']      = $this->template ?? null;
        $axis['lines']         = $this->lines ?? null;
        $axis['lineColor']     = $this->lineColor ?? null;
        $axis['lineShape']     = $this->lineShape->value ?? null;
        return array_filter($axis, function ($value) {
            return $value !== null;
        });
    }

    /** @API */
    public function setLineShape(LineShape $lineShape): self
    {
        $this->lineShape = $lineShape;
        return $this;
    }

    /** @API */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /** @API */
    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    /** @API */
    public function setStart(int $start): self
    {
        $this->start = $start;
        return $this;
    }

    /** @API */
    public function setEnd(int $end): self
    {
        $this->end = $end;
        return $this;
    }

    /** @API */
    public function setStep(int $step): self
    {
        $this->step = $step;
        return $this;
    }

    /** @API */
    public function setDecimalDigits(int $decimalDigits): self
    {
        $this->decimalDigits = $decimalDigits;
        return $this;
    }

    /** @API */
    public function setTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }

    /** @API */
    public function setLines(bool $lines): self
    {
        $this->lines = $lines;
        return $this;
    }

    /** @API */
    public function setLineColor(string $lineColor): self
    {
        $this->lineColor = $lineColor;
        return $this;
    }
}
