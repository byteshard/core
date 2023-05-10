<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Scheduler;

use DateTimeInterface;

class DateClass
{
    private DateTimeInterface $date;
    private array    $classes;

    public function __construct(DateTimeInterface $date, string ...$classes)
    {
        $this->date    = $date;
        $this->classes = $classes;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function addClasses(string ...$classes): self
    {
        $this->classes = array_merge($this->classes, $classes);
        return $this;
    }
}
