<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Enum\Calendar;

/**
 * Class CalendarMinutes
 * @package byteShard\Form\Enum\Calendar
 */
enum Minutes: int
{
    case INTERVAL_1  = 1;
    case INTERVAL_5  = 5;
    case INTERVAL_10 = 10;
    case INTERVAL_15 = 15;
}
