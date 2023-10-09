<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;

use byteShard\Form\Enum\Calendar\WeekStart;
use byteShard\Internal\Form;
use byteShard\Enum;
use byteShard\Session;
use DateTime;

/**
 * Class Calendar
 * @package byteShard\Form\Control
 */
class Calendar extends Form\FormObject implements Form\InputWidthInterface, Form\OnlyReadInterface
{
    use Form\CalendarPosition;
    use Form\ClassName;
    use Form\DateFormat;
    use Form\Disabled;
    use Form\EnableTime;
    use Form\Hidden;
    use Form\Info;
    use Form\InputHeight;
    use Form\InputLeft;
    use Form\InputTop;
    use Form\InputWidth;
    use Form\Label;
    use Form\LabelAlign;
    use Form\LabelHeight;
    use Form\LabelLeft;
    use Form\LabelTop;
    use Form\LabelWidth;
    use Form\MinutesInterval;
    use Form\Name;
    use Form\Note;
    use Form\OffsetLeft;
    use Form\OffsetTop;
    use Form\Position;
    use Form\OnlyRead;
    use Form\Required;
    use Form\ServerDateFormat;
    use Form\ShowWeekNumbers;
    use Form\Tooltip;
    use Form\Userdata;
    use Form\Validate;
    use Form\WeekStart;

    protected string                  $type                   = 'calendar';
    protected string                  $displayedTextAttribute = 'label';
    private string|int|float|DateTime $initial_value;
    protected array                   $objectParameter        = [];
    protected static Enum\Cast        $cast                   = Enum\Cast::DATE;
    private bool                      $monthSelector          = false;

    /**
     * db_column_type is empty string instead of null or specific type
     * this is intended, now the SharedParent will evaluate the default value in the framework object unless a specific type has been set
     */
    // TODO: figure out a way how to handle default column types. Probably not needed anymore anyway due to the change of prepared statements in PDO vs mysqli
    //protected ?Enum\DB\ColumnType $dbColumnType = '';
    protected string  $localization = 'de';

    public function __construct($id)
    {
        parent::__construct($id);
        $locale = strtolower(Session::getPrimaryLocale());
        if ($locale !== 'en') {
            $this->objectParameter['loadUserLanguage'] = $locale;
        }
        // the format the client will send back to the server after submit
        //$this->setServerDateFormat('%d.%m.%Y 00:00:00');
        // the format the client will display
        //$this->setDateFormat('%d.%m.%Y');
        $this->setWeekStart(WeekStart::MONDAY);
    }

    /**
     * @API
     */
    public function setLocalization(string $localization): self
    {
        $this->localization = $localization;
        return $this;
    }

    /**
     * @API
     */
    public function useOnlyMonthSelector(): self
    {
        $this->monthSelector = true;
        return $this;
    }

    public function isOnlyMonthSelector(): bool
    {
        return $this->monthSelector;
    }

    /**
     * the initial value of the Form Object
     */
    public function setValue(string|int|float|DateTime $stringOrInt): self
    {
        $this->initial_value = $stringOrInt;
        return $this;
    }

    public function getInitialValue(): string|int|float|DateTime
    {
        return $this->initial_value ?? '';
    }

    public function setDate(DateTime $date): self
    {
        if (array_key_exists('serverDateFormat', $this->attributes)) {
            $this->attributes['value'] = $date->format(str_replace('%', '', $this->attributes['serverDateFormat']));
        }
        return $this;
    }
}
