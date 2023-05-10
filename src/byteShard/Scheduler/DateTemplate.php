<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Scheduler;

use DateTime;
use DateTimeZone;
use DatePeriod;

interface DateTemplate
{
    /**
     * @param DateTime $from
     * @param DateTime $to
     * @param DateTimeZone $clientTimeZone
     * @param DatePeriod $dayIntervalPeriod
     * @return DateClass[]
     */
    public function defineDateTemplate(DateTime $from, DateTime $to, DateTimeZone $clientTimeZone, DatePeriod $dayIntervalPeriod): array;

    public function getClassTemplateArray(DateClass ...$classes): array;
}
