<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\ID;

use DateTime;
use DateTimeZone;

class DateIDElement implements IDElementInterface
{
    private DateTime      $date;
    private ?DateTimeZone $timeZone;

    public function __construct(DateTime $date, ?DateTimeZone $timeZone = null)
    {
        $this->date     = $date;
        $this->timeZone = $timeZone;
    }

    public function getId(): string
    {
        return ID::DATEID;
    }

    public function getValue(): string
    {
        return $this->date->format('Y-m-d H:i:s');
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getTimeZone(): ?DateTimeZone
    {
        return $this->timeZone;
    }

    public function getIdElement(): array
    {
        return [ID::DATEID => $this->date];
    }
}