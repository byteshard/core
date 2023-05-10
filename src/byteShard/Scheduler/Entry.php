<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Scheduler;

use DateTime;
use DateTimeInterface;
use DateTimeZone;

class Entry
{
    private DateTimeInterface $start;
    private DateTimeInterface $end;
    private string $subject;
    public function __construct(string $subject, DateTimeInterface $start, DateTimeInterface $end) {
        $this->subject = $subject;
        $this->start = $start;
        $this->end = $end;
    }

    public function getEntry(DateTimeZone $clientTimeZone): array
    {
        $start = DateTime::createFromInterface($this->start);
        $end = DateTime::createFromInterface($this->end);
        //$start->setTimezone($clientTimeZone);
        //$end->setTimezone($clientTimeZone);
        return [
            'id' => 1,
            'text' => $this->subject,
            'start_date' => $start->format('Y-m-d H:i'),
            'end_date' => $end->format('Y-m-d H:i')
        ];
    }
}