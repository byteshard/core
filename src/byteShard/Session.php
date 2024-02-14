<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard;

use byteShard\Authentication\UserDataInterface;
use byteShard\Crypto\Symmetric;
use byteShard\ID\CellIDElement;
use byteShard\ID\TabIDElement;
use byteShard\Internal\LayoutContainer;
use byteShard\Internal\PopupInterface;
use byteShard\Internal\Session as InternalSession;
use DateTimeZone;
use JetBrains\PhpStorm\Pure;

/**
 * Class Session
 * @package byteShard
 * This class will replace all calls to the current Session object.
 * This way we can replace session reliance one step at a time at one single location without having to refactor the whole code.
 */
class Session
{
    public static function getNavigationArray(bool $debug, ?string $dhtmlxCssImagePath): array
    {
        return self::getSessionObject()?->getNavigationArray($debug, $dhtmlxCssImagePath) ?? [];
    }

    #[Pure]
    public static function getCryptoKey(): string
    {
        return self::getSessionObject()?->getCryptoKey() ?? '';
    }

    public static function legacyGetSelectedTab(): string
    {
        return self::getSessionObject()?->getSelectedTab() ?? '';
    }

    public static function getUsername(): ?string
    {
        return self::getSessionObject()?->getUsername();
    }

    public static function getUserId(): ?int
    {
        return self::getSessionObject()?->getUserID();
    }

    public static function getID(string $id, string $namespace, string $type = ''): mixed
    {
        $cell = Session::getCell(\byteShard\ID\ID::refactor($namespace));

        if ($cell instanceof Cell) {
            if ($type !== '') {
                switch ($type) {
                    case 'selected':
                        return $cell->getSelectedId()?->getId($id);
                    /*case 'link':
                        $clickedLinkId = $cell->getClickedLinkId();
                        if (!empty($clickedLinkId)) {
                            $targetIds = ID::explode($clickedLinkId);
                            if (isset($targetIds->{$id})) {
                                return $targetIds->{$id};
                            }
                        }
                        break;*/
                    case 'date':
                        $selectedId = $cell->getSelectedId();
                        if (!empty($selectedId)) {
                            return $selectedId;
                        }
                        break;
                }
            } else {
                // first check for selectedID and return if found
                $selectedId = $cell->getSelectedId();
                return $selectedId?->getId($id);
                /*if (!empty($selectedId)) {
                    if (isset($selectedId[$id])) {
                        return $selectedId[$id];
                    }
                    $targetIds = ID::explode($selectedId);
                    if (isset($targetIds->{$id})) {
                        return $targetIds->{$id};
                    }
                }
                // then check for clickedLinkID and return if found
                $clickedLinkId = $cell->getClickedLinkId();
                if (!empty($clickedLinkId)) {
                    $targetIds = ID::explode($clickedLinkId);
                    if (isset($targetIds->{$id})) {
                        return $targetIds->{$id};
                    }
                }*/
            }
        }
        return null;
    }

    public static function removePopup(string $popupId): void
    {
        self::getSessionObject()?->removePopup($popupId);
    }

    public static function removeTab(\byteShard\ID\ID $tabId): void
    {
        self::getSessionObject()?->removeTab($tabId);
    }

    public static function addPopup(PopupInterface $popup): bool
    {
        $session = self::getSessionObject();
        if ($session !== null) {
            $session->addPopup($popup);
            return true;
        }
        return false;
    }

    public static function addCells(Cell ...$cells): void
    {
        self::getSessionObject()?->addCells(...$cells);
    }

    public static function setSavedCellSize(string $cell, string $type, int $dimension): void
    {
        self::getSessionObject()?->setSavedCellSize($cell, $type, $dimension);
    }

    public static function setSavedCellCollapse(string $cell): void
    {
        self::getSessionObject()?->setSavedCellCollapse($cell);
    }

    public static function getSizeData(string $name): array
    {
        return self::getSessionObject()?->getSizeData($name) ?? [];
    }

    public static function getAdditionalUserdata(): array
    {
        return self::getSessionObject()?->getAdditionalUserData() ?? [];
    }

    public static function setAdditionalUserData(array $userData): void
    {
        self::getSessionObject()?->setAdditionalUserData($userData);
    }

    public static function setClientTimeZone(DateTimeZone $timeZone): void
    {
        self::getSessionObject()?->setClientTimeZone($timeZone);
    }

    public static function getCell(?\byteShard\ID\ID $id): ?Cell
    {
        return self::getSessionObject()?->getCell($id);
    }

    public static function getTab(\byteShard\ID\ID $id): ?LayoutContainer
    {
        return self::getSessionObject()?->getTab($id);
    }

    public static function getLocaleForAllObjects(string $locale): array
    {
        return self::getSessionObject()?->getLocaleForAllObjects($locale) ?? [];
    }

    public static function getPopup(string $popupId): ?Popup
    {
        return self::getSessionObject()?->getPopup($popupId);
    }

    public static function setSelectedTab(ID\ID $id): void
    {
        self::getSessionObject()?->setSelectedTab($id);
    }

    public static function getIdByFQCN(string $fqcn): \byteShard\ID\ID
    {
        return \byteShard\ID\ID::factory(new CellIDElement($fqcn), new TabIDElement(Session::legacyGetSelectedTab()));
    }

    public static function getPermissionAccessType(string $permission): int
    {
        return self::getSessionObject()?->getPermissionAccessType($permission) ?? 0;
    }

    public static function getPermissionIDArray(string $permission): array
    {
        return self::getSessionObject()?->getPermissionIDArray($permission) ?? [];
    }

    public static function registerCell(Cell $cell): void
    {
        self::getSessionObject()?->registerCell($cell);
    }

    public static function getLocale(): string
    {
        $locale  = '';
        $session = self::getSessionObject();
        if ($session !== null) {
            $locale = $session->getLocale();
        }
        return $locale !== '' ? $locale : 'en';
    }

    public static function getPrimaryLocale(): string
    {
        $session = self::getSessionObject();
        if ($session !== null) {
            $locale      = $session->getLocale();
            $localeArray = explode('_', $locale);
            if (isset($localeArray[0])) {
                return $localeArray[0];
            }
        }
        return 'en';
    }

    public static function encryptID(string $id, int $navigationLevel = null): string
    {
        $session = self::getSessionObject();
        if ($session !== null) {
            return $session->encryptID($id, $navigationLevel);
        }
        return $id;
    }

    #[Pure]
    public static function getEncryptedIDs(): array
    {
        $session = self::getSessionObject();
        if ($session !== null) {
            return $session->getEncryptedIDs();
        }
        return [
            'id'               => [],
            'navigation_level' => []
        ];
    }

    public static function getTopLevelNonce(): string
    {
        $session = self::getSessionObject();
        if ($session !== null) {
            return $session->getTopLevelNonce();
        }
        throw new Exception('', 2);
    }

    public static function encrypt(string|false $message, string $nonce = ''): string
    {
        if ($message === false) {
            return '';
        }
        return Symmetric::encrypt($message, self::getCryptoKey(), true, $nonce);
    }

    public static function decrypt(string $message): string
    {
        return Symmetric::decrypt($message, self::getCryptoKey());
    }

    public static function checkNonce(string $message, string $nonce): bool
    {
        return Symmetric::checkNonce($message, $nonce);
    }

    public static function setUserData(UserDataInterface $userData, string $lastTab): void
    {
        $session = self::getSessionObject();
        if ($session !== null) {
            $serviceAccount = $userData->isServiceAccount();
            $session->setUserdata($userData->getUserId(), $userData->getUsername(), $lastTab, $serviceAccount);
        }
    }

    public static function getClientTimeZone(): DateTimeZone
    {
        return self::getSessionObject()?->getClientTimeZone() ?? new DateTimeZone('UTC');
    }

    private static function getSessionObject(): ?InternalSession
    {
        if (isset($_SESSION[MAIN]) && $_SESSION[MAIN] instanceof InternalSession) {
            return $_SESSION[MAIN];
        }
        return null;
    }
}
