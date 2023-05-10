<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Event;

use byteShard\Event\OnChangeInterface;
use byteShard\Event\OnCheckInterface;
use byteShard\Event\OnClickInterface;
use byteShard\Event\OnEmptyClickInterface;
use byteShard\Event\OnEnterInterface;
use byteShard\Event\OnInputChangeInterface;
use byteShard\Event\OnSelectInterface;
use byteShard\Event\OnStateChangeInterface;
use byteShard\Event\OnUncheckInterface;
use byteShard\Event\OnUploadInterface;
use byteShard\Form\Event\OnButtonClick;
use byteShard\Form\Event\OnChange;
use byteShard\Form\Event\OnCheck;
use byteShard\Form\Event\OnInputChange;
use byteShard\Form\Event\OnUnCheck;
use byteShard\Form\Event\OnUploadFile;
use byteShard\Internal\Action;
use byteShard\Internal\Action\ClientExecutionInterface;
use byteShard\Internal\CellContent;
use byteShard\Scheduler\Event\OnEmptyClick;

/**
 * Class Event
 * @package byteShard\Internal\Event
 */
abstract class Event
{
    /** @var ClientExecutionInterface[]|Action[] */
    private array           $actions          = [];
    protected static string $event            = '';
    protected static string $function         = '';
    protected static string $contentEventName = '';

    // usually the event is triggered by the same event, but for example onCheck is in fact an onChange event, the if checked/unchecked logic is implemented serverside
    protected static string $deviatingEvent = '';

    /**
     * Event constructor.
     * @param ClientExecutionInterface|Action ...$actions
     */
    public function __construct(ClientExecutionInterface|Action ...$actions)
    {
        $this->addActions(...$actions);
    }

    private function checkDeprecation(Action ...$actions): void
    {
        $deprecatedCallIn = '';
        $line             = 0;
        foreach ($actions as $action) {
            if ($deprecatedCallIn !== '') {
                break;
            }
            if (!$action instanceof ClientExecutionInterface || $action->getClientExecution() === false) {
                foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $step) {
                    if (!empty($step['class']) && is_subclass_of($step['class'], CellContent::class)) {
                        $deprecatedCallIn = $step['class'];
                        break;
                    }
                    $line = $step['line'];
                }
            }
        }
        if ($deprecatedCallIn !== '') {
            $interface = match (get_called_class()) {
                OnChange::class                                              => OnChangeInterface::class,
                OnCheck::class, '\\byteShard\\Grid\\Event\\OnCheck'          => OnCheckInterface::class,
                '\\byteShard\\Toolbar\\Event\\OnClick', OnButtonClick::class => OnClickInterface::class,
                OnEmptyClick::class                                          => OnEmptyClickInterface::class,
                '\\byteShard\\Toolbar\\Event\\OnEnter'                       => OnEnterInterface::class,
                OnInputChange::class                                         => OnInputChangeInterface::class,
                '\\byteShard\\Tree\\Event\\OnSelect', '\\byteShard\\Grid\\Event\\OnSelect'        => OnSelectInterface::class,
                '\\byteShard\\Toolbar\\Event\\OnStateChange'                 => OnStateChangeInterface::class,
                OnUnCheck::class                                             => OnUncheckInterface::class,
                OnUploadFile::class                                          => OnUploadInterface::class,
                default                                                      => 'INTERFACE NOT IMPLEMENTED ('.get_called_class().')',
            };
            trigger_error('Adding Actions which are not meant to be executed on the client is deprecated. Please implement '.$interface.' instead. Called in: '.$deprecatedCallIn.' Line:'.$line, E_USER_DEPRECATED);
        }
    }

    /**
     * @param Action ...$actions
     */
    public function addActions(ClientExecutionInterface|Action ...$actions): void
    {
        $this->checkDeprecation(...$actions);
        foreach ($actions as $action) {
            $this->actions[] = $action;
        }
    }

    /**
     * @return Action[]|ClientExecutionInterface[]
     */
    public function getActionArray(): array
    {
        return $this->actions;
    }

    /**
     * @return string
     */
    public function getEventType(): string
    {
        return static::$event;
    }

    /**
     * @return string
     * @internal
     */
    public function getContentEventName(): string
    {
        return static::$contentEventName !== '' ? static::$contentEventName : static::$event;
    }

    /**
     * @return string
     * @internal
     */
    public static function getEventNameForEventHandler(): string
    {
        return static::$contentEventName !== '' ? static::$contentEventName : static::$event;
    }

    /**
     * @return string
     */
    public function getFunctionName(): string
    {
        return static::$function !== '' ? static::$function : 'do'.ucfirst(static::$event);
    }

    public static function getDeviatingEvent(): string
    {
        return static::$deviatingEvent;
    }
}
