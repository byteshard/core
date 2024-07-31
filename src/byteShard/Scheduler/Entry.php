<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Scheduler;

use byteShard\Session;
use DateTime;
use DateTimeInterface;
use DateTimeZone;

class Entry
{
    public function __construct(
        private readonly array             $id,
        private readonly string            $subject,
        private readonly DateTimeInterface $start,
        private readonly DateTimeInterface $end,
        private readonly string            $class = ''
    )
    {

    }

    public function getEntry(DateTimeZone $clientTimeZone, string $nonce): array
    {
        $start  = DateTime::createFromInterface($this->start);
        $end    = DateTime::createFromInterface($this->end);
        $id     = Session::encrypt(json_encode($this->id), $nonce);
        $result = [
            'id'         => $id,
            'text'       => $this->subject,
            'start_date' => $start->format('Y-m-d H:i'),
            'end_date'   => $end->format('Y-m-d H:i')
        ];
        if ($this->class !== '') {
            $result['class'] = $this->class;
        }
        return $result;
    }
}