<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal;

use byteShard\Action\ConfirmAction;
use byteShard\Cell;
use byteShard\Event\OnClickInterface;
use byteShard\Event\OnDoubleClickInterface;
use byteShard\Event\OnDropInterface;
use byteShard\Event\OnEmptyClickInterface;
use byteShard\Event\OnEnterInterface;
use byteShard\Event\OnInputChangeInterface;
use byteShard\Event\OnLinkClickInterface;
use byteShard\Event\OnPollInterface;
use byteShard\Event\OnPopupCloseInterface;
use byteShard\Event\OnScrollBackwardInterface;
use byteShard\Event\OnScrollForwardInterface;
use byteShard\Event\OnSelectInterface;
use byteShard\Event\OnStateChangeInterface;
use byteShard\Event\OnUploadInterface;
use byteShard\ID\ID;
use byteShard\Event\EventResult;
use byteShard\Event\OnChangeInterface;
use byteShard\Event\OnCheckInterface;
use byteShard\Event\OnUncheckInterface;
use byteShard\Form\Control\Checkbox;
use byteShard\ID\TabIDElement;
use byteShard\Internal\Action\ControlIdInterface;
use byteShard\Internal\Action\ExportInterface;
use byteShard\Internal\Struct\ClientData;
use byteShard\Internal\Struct\GetData;
use byteShard\Popup;
use byteShard\Session;
use byteShard\Tab;
use DateTimeZone;
use Closure;

class ActionCollector
{
    public static function getEventActions(
        ?Cell         $cell,
        ID            $id,
        string        $eventInterface,
        string        $eventId,
        string        $objectValue,
        string        $confirmationId,
        ?ClientData   $clientData,
        ?GetData      $getData,
        ?DateTimeZone $clientTimeZone,
        ?array        $objectProperties,
        string        $eventType,
        ?Closure      $getCellContentCallback = null,
        string        $className = ''): array
    {
        if ($eventInterface === OnChangeInterface::class) {
            $row = $clientData?->getRows() ?? [];
            if (isset($row[0], $row[0]->{$eventId}) && $row[0]->{$eventId}->type === Checkbox::class) {
                if ($row[0]->{$eventId}->value === true) {
                    $eventInterface = OnCheckInterface::class;
                } else {
                    $eventInterface = OnUncheckInterface::class;
                }
            }
        }
        if ($className === '') {
            $className = $cell?->getContentClass() ?? '';
        }
        $actions = $cell !== null ? self::getDeprecatedSessionActions($cell, $eventInterface, $eventId, $objectValue) : [];
        if (empty($actions) && $eventInterface !== '') {
            if (isset(class_implements($className)[$eventInterface])) {
                $eventMethod = self::getInterfaceMethod($eventInterface);
                if ($eventMethod !== '') {
                    if ($getCellContentCallback !== null) {
                        $class = $getCellContentCallback();
                    } else {
                        // Popup
                        $class = new $className();
                        if ($class instanceof Popup) {
                            $class->addTabIdElement(new TabIDElement($id->getTabId()));
                        }
                    }
                    if ($clientData !== null) {
                        $class->setProcessedClientData($clientData);
                    }
                    $eventResult = $class->{$eventMethod}();
                    /** @var EventResult $eventResult */
                    if ($eventResult !== null) {
                        $actions = $eventResult->getResultActions($eventId, $objectValue);
                    }
                } else {
                    Debug::debug('Event interface undefined: '.$eventInterface);
                }
            }
        }
        return self::initializeActions($actions, $id, $cell, $eventId, $confirmationId, $clientData, $getData, $clientTimeZone, $objectProperties, $eventType, $objectValue);
    }

    /**
     * @return Action[]
     */
    private static function getDeprecatedSessionActions(Cell $cell, string $eventInterface, string $eventId, string $objectValue): array
    {
        $actions = match ($eventInterface) {
            OnSelectInterface::class => $cell->getContentActions('onSelect'),
            default                  => $cell->getActionsForEvent($eventId),
        };
        if ($objectValue !== '') {
            $actions = self::filterObjectValueActions($actions, $objectValue);
        }
        return $actions;
    }

    private static function filterObjectValueActions(array $actions, string $objectValue): array
    {
        $filteredActions = [];
        foreach ($actions as $objectId => $radioActions) {
            if ($objectId === $objectValue) {
                $filteredActions = array_merge($filteredActions, $radioActions);
            }
        }
        return $filteredActions;
    }

    private static function getInterfaceMethod(string $eventInterface): string
    {
        return match ($eventInterface) {
            OnChangeInterface::class         => 'onChange',
            OnCheckInterface::class          => 'onCheck',
            OnClickInterface::class          => 'onClick',
            OnDoubleClickInterface::class    => 'onDoubleClick',
            OnDropInterface::class           => 'onDrop',
            OnEmptyClickInterface::class     => 'onEmptyClick',
            OnEnterInterface::class          => 'onEnter',
            OnInputChangeInterface::class    => 'onInputChange',
            OnLinkClickInterface::class      => 'onLinkClick',
            OnPollInterface::class           => 'onPoll',
            OnPopupCloseInterface::class     => 'onPopupClose',
            OnScrollBackwardInterface::class => 'onScrollBackward',
            OnScrollForwardInterface::class  => 'onScrollForward',
            OnSelectInterface::class         => 'onSelect',
            OnStateChangeInterface::class    => 'onStateChange',
            OnUncheckInterface::class        => 'onUncheck',
            OnUploadInterface::class         => 'onUpload',
            default                          => '',
        };
    }

    /**
     * @param Action[] $actions
     */
    public static function initializeActions(array $actions, ID $id, ?Cell $cell, string $eventId, string $confirmationId, ?ClientData $clientData, ?GetData $getData, ?DateTimeZone $clientTimeZone, ?array $objectProperties, string $eventType, string $objectValue): array
    {
        $tab = null;
        if ($cell === null) {
            $tab = Session::getTab($id);
        }
        foreach ($actions as $key => $action) {
            $actions[$key] = self::initializeAction($action, $id, $cell, $tab, $eventId, $confirmationId, $clientData, $getData, $clientTimeZone, $objectProperties, $eventType, $objectValue);
        }
        return $actions;
    }

    public static function initializeAction(Action $action, ?ID $id, ?Cell $cell, ?LayoutContainer $tab, string $eventId, string $confirmationId, ?ClientData $clientData, ?GetData $getData, ?DateTimeZone $clientTimeZone, ?array $objectProperties, string $eventType = '', string $objectValue = ''): Action
    {
        $action->setClientTimeZone($clientTimeZone);
        if ($confirmationId !== '' && $id !== null) {
            $action->setConfirmed($confirmationId, $id->getEncryptedId());
        }
        if ($clientData !== null) {
            $action->setClientData($clientData);
        }
        if ($getData !== null) {
            $action->setGetData($getData);
        }
        if ($objectProperties !== null) {
            $action->setObjectProperties($objectProperties);
        }
        if ($action instanceof ExportInterface && $id !== null) {
            $exportAction = clone $action;
            $exportAction->setEventID($id, $eventId);
            unset($action);
            $action = $exportAction;
        } elseif ($action instanceof ControlIdInterface) {
            $action->setControlID($eventId);
        }
        if ($cell !== null) {
            $action->initActionInCell($cell);
        } elseif ($tab instanceof Tab) {
            $action->initActionInTab($tab);
        }
        if ($eventType !== '' && $action instanceof ConfirmAction) {
            $action->setEventType($eventType);
            $action->setObjectValue($objectValue);
        }
        return $action;
    }
}
