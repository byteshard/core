<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Session;

use byteShard\Cell;
use byteShard\ID;
use byteShard\Internal\LayoutContainer;
use byteShard\Internal\PopupInterface;
use byteShard\Popup;

class SessionPopups
{
    /**
     * @var Popup[]
     */
    private array $popups = [];

    /**
     * @param string $popupId
     * @return Popup|null
     */
    public function getPopup(string $popupId): ?Popup
    {
        if (array_key_exists($popupId, $this->popups)) {
            return $this->popups[$popupId];
        }
        return null;
    }

    public function removePopup(string $id): void
    {
        if (isset($this->popups[$id])) {
            if ($this->popups[$id] instanceof LayoutContainer) {
                //remove all contained data before removing the popup itself
                $this->popups[$id]->removeLayoutContainer();
            }
            unset($this->popups[$id]);
        }
    }

    /**
     *
     * @param Popup $popup
     * @internal
     */
    public function addPopup(PopupInterface $popup): void
    {
        $id      = $popup->getNewId();
        $popupId = $id->getEncodedContainerId();
        // Closure cannot be serialized, remove it before it's added to the session. Will be evaluated in OpenPopup already
        $popup->resetCondition();
        $this->popups[$popupId] = $popup;

        //TODO: check if there is a downside to not checking if the popup is already registered in the session
        //if yes, get any changing content (e.g. the Popup\Condition) from the passed popup and set it in the session
        /*if (!isset($this->popups[$id])) {
            $this->popups[$id] = $popup;
        }*/
    }

    public function popupExists(ID\ID $id): bool
    {
        return array_key_exists($id->getEncodedContainerId(), $this->popups);
    }

    public function getCell(ID\ID $id): ?Cell
    {
        return $this->popups[$id->getEncodedContainerId()]?->getCell($id);
    }
}
