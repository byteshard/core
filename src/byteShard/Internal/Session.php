<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal;

use byteShard\Crypto;
use byteShard\Cell;
use byteShard\Internal\Session\EncryptedIDStorageInterface;
use byteShard\Internal\Session\SessionPopups;
use byteShard\Popup;
use byteShard\ID;
use byteShard\Tab;
use byteShard\Permission;
use byteShard\TabNew;
use DateTimeZone;
use DateTime;
use byteShard\Enum;
use byteShard\Exception;

/**
 * Class Session
 * @package byteShard\Internal
 */
class Session implements TabParentInterface, EncryptedIDStorageInterface
{
    private array         $meta                    = [];
    private array         $cell_size               = [];
    private array         $encrypted_id            = [];
    private array         $encrypted_navigation_id = [];
    private array         $user                    = [];
    private array         $date                    = [];
    private array         $default_db_column_type  = [];
    private array         $meta_db_column_type     = [];
    private array         $cells                   = [];
    private bool          $enable_test_methods     = false;
    private ?string       $url;
    private string        $cryptoKey;
    private string        $topLevelNonce;
    private ?Permission   $permissions             = null;
    private SessionTabs   $tabs;
    private SessionPopups $popups;
    private SessionLocale $sessionLocale;
    private SessionUpload $upload;

    /**
     * Session constructor.
     * @param string $locale
     * @throws Exception
     */
    public function __construct(string $locale)
    {
        $this->cryptoKey     = Crypto::randomBytes(32);
        $this->topLevelNonce = Crypto::randomBytes(24);
        $this->tabs          = new SessionTabs();
        $this->sessionLocale = new SessionLocale($locale);
        $this->upload        = new SessionUpload();
        $this->popups        = new SessionPopups();
    }

    public function getCryptoKey(): string
    {
        return $this->cryptoKey;
    }

    public function getTopLevelNonce(): string
    {
        return $this->topLevelNonce;
    }

    public function setIDForFQCN(string $fqcn, $id): void
    {
        trigger_error(__METHOD__.' has been deprecated. There is no substitute method.', E_USER_DEPRECATED);
    }

    /**
     * @return string the current locale
     */
    public function getLocale(): string
    {
        return $this->sessionLocale->getLocale();
    }

    /**
     * enable the ability to set the user ID during runtime
     */
    public function enableTestMethods()
    {
        $this->enable_test_methods = true;
    }

    public function setUserID($user_id)
    {
        if ($this->enable_test_methods === true) {
            $this->user['User_ID'] = $user_id;
        }
    }

    public function setUsername($username)
    {
        if ($this->enable_test_methods === true) {
            $this->user['Username'] = $username;
        }
    }


    public function getSelectedTab(): string
    {
        return $this->tabs->getSelectedTab();
    }

    public function setSelectedTab(ID\ID $id): bool
    {
        $this->tabs->setSelectedTab($id->getTabId());
        return true;
    }

    public function getTab(ID\ID $id): ?Tab
    {
        return $this->tabs->getTab($id);
    }

    public function getTabName(ID\ID $tabId): ?string
    {
        return $this->tabs->getTab($tabId)?->getName() ?? null;
    }

    public function getPopup(string $popupId): ?Popup
    {
        return $this->popups->getPopup($popupId);
    }

    public function removePopup(string $id): void
    {
        $this->popups->removePopup($id);
    }

    /**
     *
     * @param Popup $popup
     * @internal
     */
    public function addPopup(PopupInterface $popup): void
    {
        $this->popups->addPopup($popup);
        $cells = $popup->getCells();
        foreach ($cells as $cell) {
            $cellId = $cell->getNewId()?->getEncodedCellId(false, false);
            if ($cellId !== null && !array_key_exists($cellId, $this->cells)) {
                $this->cells[$cellId] = $cell;
            }
        }
    }

    public function addCells(Cell ...$cells): void
    {
        foreach ($cells as $cell) {
            $cellId = $cell->getNewId()?->getEncodedCellId(false, false);
            if ($cellId !== null && !array_key_exists($cellId, $this->cells)) {
                $this->cells[$cellId] = $cell;
            }
        }
    }

    /**
     * @param ID\ID|null $id
     * @return Cell|null
     */
    public function getCell(?ID\ID $id): ?Cell
    {
        if ($id === null) {
            return null;
        }
        if ($id->getContainerId() !== '') {
            // TODO: migrate the cells out of the session. To start find all cell attributes which gannot be calculated and remove them from the cell
            // store all other attributes in a new sessionObject (e.g. SessionCellMigrationHelper)
            // then create empty cells here, inject all stored data and return the fresh cell
            // lastly move all stored attributes in SessionCellMigrationHelper in either the Database (e.g. Cell Storage) or the client (e.g. a JWT)
            $newCellId = clone $id;
            $newCellId->addIdElement(new ID\TabIDElement($this->getSelectedTab()));
            $cell = new Cell();
            $cell->init('', $newCellId);
            return $cell;
        }
        if ($id->isCellId() === true) {
            $cellId = $id->getEncodedCellId(false, false);
            if (array_key_exists($cellId, $this->cells)) {
                return $this->cells[$cellId];
            }
            // deprecated tab implementation
            if (!$id->isPopupId()) {
                //TODO: Popup cells should be under ->cells as well
                $split = explode('\\', $id->getCellId());
                array_pop($split);
                $tryPopupId = clone $id;
                $tryPopupId->addIdElement(new ID\PopupIDElement(implode('\\', $split)));
                if ($this->popups->popupExists($tryPopupId)) {
                    return $this->popups->getCell($tryPopupId);
                }
                $encryptedTabId = $id->getEncryptedContainerId();
            }
        }
        return null;
    }

    /**
     * @param Cell $cell
     * @param ?string $upload_control_name the name of the upload object that is declared in the form
     * @param ?string $encrypted_client_name the encrypted upload_control_name
     * @param array $fileTypeArray
     * @param string $method
     * @param string $targetFilename
     * @param string $targetPath
     * @param bool $clearAfterUpload
     * @return null|string
     */
    public function getUploadId(Cell $cell, ?string $upload_control_name, ?string $encrypted_client_name, array $fileTypeArray = [], string $method = '', string $targetFilename = '', string $targetPath = '', bool $clearAfterUpload = false): ?string
    {
        return $this->upload->getUploadId($cell, $upload_control_name, $encrypted_client_name, $fileTypeArray, $method, $targetFilename, $targetPath, $clearAfterUpload, self::getCryptoKey());
    }

    public function setUploadFileData(string $upload_object_id, string $filename, string $path, string $client_name): void
    {
        $this->upload->setUploadFileData($upload_object_id, $filename, $path, $client_name);
    }

    public function encryptID(string $id, ?int $level = null): string
    {
        if (array_key_exists($id, $this->encrypted_id)) {
            return $this->encrypted_id[$id];
        }
        $this->encrypted_id[$id] = Encrypt::encrypt($id);
        if ($level !== null) {
            $this->encrypted_navigation_id[$this->encrypted_id[$id]] = $level;
        }
        return $this->encrypted_id[$id];
    }

    public function getEncryptedIDs(): array
    {
        return array('id' => $this->encrypted_id, 'navigation_level' => $this->encrypted_navigation_id);
    }

    public function setUserdata(int $userId, string $userName, string $lastTabName = '', bool $serviceAccount = false): void
    {
        $this->url                           = BS_WEB_ROOT_DIR;
        $this->user['LoggedIn']              = true;
        $this->user['UserID']                = $userId; // TODO: alles von UserID auf User_ID umstellen
        $this->user['User_ID']               = $userId;
        $this->user['Username']              = $userName;
        $this->user['access']                = true;
        $this->user['serviceAccount']        = $serviceAccount;
        $this->user['timeOfLastUserRequest'] = time();
        $this->user['AdditionalData']        = [];
        $this->tabs->setSelectedTab($lastTabName);
    }

    public function setAdditionalUserData(array $userData): void
    {
        $this->user['AdditionalData'] = $userData;
    }

    /**
     * @return array
     */
    public function getAdditionalUserData(): array
    {
        if (isset($this->user['AdditionalData']) && is_array($this->user['AdditionalData'])) {
            return $this->user['AdditionalData'];
        }
        return [];
    }

    public function getServerAddress(): ?string
    {
        return $this->url ?? null;
    }

    /**
     * @return null|int
     */
    public function getUserID(): ?int
    {
        if (isset($this->user['User_ID']) && !empty($this->user['User_ID'])) {
            return $this->user['User_ID'];
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getUsername(): ?string
    {
        if (isset($this->user['Username']) && !empty($this->user['Username'])) {
            return $this->user['Username'];
        }
        return null;
    }

    /**
     * @return bool
     */
    public function getLoginState(): bool
    {
        if (isset($this->user['LoggedIn']) && $this->user['LoggedIn'] === true) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isServiceAccount(): bool
    {
        if (isset($this->user['serviceAccount']) && $this->user['serviceAccount'] === true) {
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function setServiceAccount(): void
    {
        $this->user['serviceAccount'] = true;
    }

    /**
     * @return null|int
     */
    public function getTimeOfLastUserRequest(): ?int
    {
        if (isset($this->user['timeOfLastUserRequest'])) {
            return $this->user['timeOfLastUserRequest'];
        }
        return null;
    }

    /**
     *
     */
    public function setTimeOfLastUserRequest(): void
    {
        $this->user['timeOfLastUserRequest'] = time();
    }

    /**
     * @param bool|null $bool
     * @return bool|null
     */
    public function permissionsAreInitialized(?bool $bool = null): ?bool
    {
        if ($bool === null) {
            if (isset($this->user['permissionsAreInitialized']) && $this->user['permissionsAreInitialized'] === true) {
                return true;
            }
            return false;
        }
        $this->user['permissionsAreInitialized'] = $bool;
        return null;
    }

    /**
     * @param null|bool $bool
     * @return bool|null
     */
    public function tabsAreInitialized(?bool $bool = null): ?bool
    {
        if ($bool === null) {
            if (isset($this->user['tabsAreInitialized']) && $this->user['tabsAreInitialized'] === true) {
                return true;
            }
            return false;
        }
        $this->user['tabsAreInitialized'] = $bool;
        return null;
    }

    /**
     * @param bool|null $bool
     * @return bool|null
     */
    public function cellSizesAreLoaded(?bool $bool = null): ?bool
    {
        if ($bool === null) {
            if (isset($this->user['cellSizesAreLoaded']) && $this->user['cellSizesAreLoaded'] === true) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->user['cellSizesAreLoaded'] = $bool;
        }
        return null;
    }

    public function setPermissionObject(Permission $permissionObject): void
    {
        $this->permissions = $permissionObject;
    }

    public function setLoginError(): void
    {
        $this->meta['loginError'] = true;
    }

    public function getLoginErrorState(): bool
    {
        if (isset($this->meta['loginError']) && $this->meta['loginError'] === true) {
            return true;
        } else {
            return false;
        }
    }

    public function clearLoginError(): void
    {
        if (isset($this->meta['loginError'])) {
            unset($this->meta['loginError']);
        }
    }

    public function setUserLoggedOut(): void
    {
        $this->meta['userLoggedOut'] = true;
    }

    public function clearUserLoggedOut(): void
    {
        if (isset($this->meta['userLoggedOut'])) {
            unset($this->meta['userLoggedOut']);
        }
    }

    public function getUserLoggedOut(): bool
    {
        if (isset($this->meta['userLoggedOut']) && $this->meta['userLoggedOut'] === true) {
            return true;
        }
        return false;
    }

    public function setClientTimeZone(DateTimeZone $timeZone): void
    {
        $this->date['client_timezone'] = $timeZone;
    }

    public function setTimezones(DateTimeZone|string $clientTimezone, DateTimeZone|string $dbTimezone): void
    {
        if ($clientTimezone instanceof DateTimeZone) {
            $this->date['client_timezone'] = $clientTimezone;
        } else {
            $this->date['client_timezone'] = new DateTimeZone($clientTimezone);
        }
        if ($dbTimezone instanceof DateTimeZone) {
            $this->date['db_timezone'] = $dbTimezone;
        } else {
            $this->date['db_timezone'] = new DateTimeZone($dbTimezone);
        }
    }

    /**
     * @param $db_column_date_format
     * @param $db_column_smalldatetime_format
     * @param $db_column_datetime_format
     * @param $db_column_datetime_precision
     * @param $db_column_datetime2_format
     * @param $db_column_datetime2_precision
     * @param $db_column_datetimeoffset_format
     * @param $db_column_datetimeoffset_precision
     * @param $db_column_bigintdate_format
     * @param $db_column_time_format
     * @param $db_column_time_precision
     */
    public function setDBFormats($db_column_date_format, $db_column_smalldatetime_format, $db_column_datetime_format, $db_column_datetime_precision, $db_column_datetime2_format, $db_column_datetime2_precision, $db_column_datetimeoffset_format, $db_column_datetimeoffset_precision, $db_column_bigintdate_format, $db_column_time_format, $db_column_time_precision)
    {
        // move those to the respective DB classes
        $this->date['db_column_date_format']              = $db_column_date_format;
        $this->date['db_column_smalldatetime_format']     = $db_column_smalldatetime_format;
        $this->date['db_column_datetime_format']          = $db_column_datetime_format;
        $this->date['db_column_datetime_precision']       = $db_column_datetime_precision;
        $this->date['db_column_datetime2_format']         = $db_column_datetime2_format;
        $this->date['db_column_datetime2_precision']      = $db_column_datetime2_precision;
        $this->date['db_column_datetimeoffset_format']    = $db_column_datetimeoffset_format;
        $this->date['db_column_datetimeoffset_precision'] = $db_column_datetimeoffset_precision;
        $this->date['db_column_bigintdate_format']        = $db_column_bigintdate_format;
        $this->date['db_column_time_format']              = $db_column_time_format;
        $this->date['db_column_time_precision']           = $db_column_time_precision;
    }

    /**
     * @param Enum\DB\ColumnType $client_form_control_calendar_default_db_column_type
     * @param Enum\DB\ColumnType $client_grid_column_calendar_default_db_column_type
     * @param Enum\DB\ColumnType $client_grid_column_date_default_db_column_type
     */
    public function setClientFormats(Enum\DB\ColumnType $client_form_control_calendar_default_db_column_type, Enum\DB\ColumnType $client_grid_column_calendar_default_db_column_type, Enum\DB\ColumnType $client_grid_column_date_default_db_column_type)
    {
        $this->default_db_column_type['form']['calendar'] = $client_form_control_calendar_default_db_column_type;
        $this->default_db_column_type['grid']['calendar'] = $client_grid_column_calendar_default_db_column_type;
        $this->default_db_column_type['grid']['date']     = $client_grid_column_date_default_db_column_type;
    }

    /**
     * @param Enum\DB\ColumnType $db_meta_data_column_created_on_column_type
     * @param Enum\DB\ColumnType $db_meta_data_column_modified_on_column_type
     * @param Enum\DB\ColumnType $db_meta_data_column_archived_on_column_type
     */
    public function setMetaColumnFormats(Enum\DB\ColumnType $db_meta_data_column_created_on_column_type, Enum\DB\ColumnType $db_meta_data_column_modified_on_column_type, Enum\DB\ColumnType $db_meta_data_column_archived_on_column_type)
    {
        $this->meta_db_column_type['created_on']  = $db_meta_data_column_created_on_column_type;
        $this->meta_db_column_type['modified_on'] = $db_meta_data_column_modified_on_column_type;
        $this->meta_db_column_type['archived_on'] = $db_meta_data_column_archived_on_column_type;
    }

    public function getDefaultDBColumnType($cell_content, $type): Enum\DB\ColumnType
    {
        if (array_key_exists($cell_content, $this->default_db_column_type) && array_key_exists($type, $this->default_db_column_type[$cell_content])) {
            return $this->default_db_column_type[$cell_content][$type];
        }
        throw new Exception(__METHOD__.': Default DB Column Type for '.$cell_content.':'.$type.' has not been defined');
    }

    public function getMetaDataDBColumnType($column): Enum\DB\ColumnType
    {
        if (array_key_exists($column, $this->meta_db_column_type)) {
            return $this->meta_db_column_type[$column];
        }
        throw new Exception(__METHOD__.': Column Type for Meta Data column "'.$column.'" has not been defined');
    }


    /**
     * @param string $type
     * @return string
     * @throws Exception
     */
    public function getDateTimeFormat(string $type): string
    {
        $colType = Enum\DB\ColumnType::tryFrom($type);
        switch ($colType) {
            case Enum\DB\ColumnType::DATE:
                if (array_key_exists('db_column_date_format', $this->date)) {
                    return $this->date['db_column_date_format'];
                }
                throw new Exception(__METHOD__.': Date format for type "'.$type.'" has not been defined');
            case Enum\DB\ColumnType::SMALLDATETIME:
                if (array_key_exists('db_column_smalldatetime_format', $this->date)) {
                    return $this->date['db_column_smalldatetime_format'];
                }
                throw new Exception(__METHOD__.': Date format for type "'.$type.'" has not been defined');
            case Enum\DB\ColumnType::DATETIME:
                if (array_key_exists('db_column_datetime_format', $this->date)) {
                    return $this->date['db_column_datetime_format'];
                }
                throw new Exception(__METHOD__.': Date format for type "'.$type.'" has not been defined');
            case Enum\DB\ColumnType::DATETIME2:
                if (array_key_exists('db_column_datetime2_format', $this->date)) {
                    return $this->date['db_column_datetime2_format'];
                }
                throw new Exception(__METHOD__.': Date format for type "'.$type.'" has not been defined');
            case Enum\DB\ColumnType::DATETIMEOFFSET:
                if (array_key_exists('db_column_datetimeoffset_format', $this->date)) {
                    return $this->date['db_column_datetimeoffset_format'];
                }
                throw new Exception(__METHOD__.': Date format for type "'.$type.'" has not been defined');
            case Enum\DB\ColumnType::BIGINT_DATE:
                if (array_key_exists('db_column_bigintdate_format', $this->date)) {
                    return $this->date['db_column_bigintdate_format'];
                }
                throw new Exception(__METHOD__.': Date format for type "'.$type.'" has not been defined');
            case Enum\DB\ColumnType::TIME:
                if (array_key_exists('db_column_time_format', $this->date)) {
                    return $this->date['db_column_time_format'];
                }
                throw new Exception(__METHOD__.': Date format for type "'.$type.'" has not been defined');
        }
        throw new Exception(__METHOD__.': Date format for type "'.$type.'" has not been defined');
    }

    /**
     * @param string $type
     * @return int
     */
    public function getDateTimePrecision(string $type): int
    {
        $type = Enum\DB\ColumnType::tryFrom($type);
        switch ($type) {
            case Enum\DB\ColumnType::DATE:
                if (array_key_exists('db_column_date_precision', $this->date)) {
                    return $this->date['db_column_date_precision'];
                }
                break;
            case Enum\DB\ColumnType::SMALLDATETIME:
                if (array_key_exists('db_column_smalldatetime_precision', $this->date)) {
                    return $this->date['db_column_smalldatetime_precision'];
                }
                break;
            case Enum\DB\ColumnType::DATETIME:
                if (array_key_exists('db_column_datetime_precision', $this->date)) {
                    return $this->date['db_column_datetime_precision'];
                }
                break;
            case Enum\DB\ColumnType::DATETIME2:
                if (array_key_exists('db_column_datetime2_precision', $this->date)) {
                    return $this->date['db_column_datetime2_precision'];
                }
                break;
            case Enum\DB\ColumnType::DATETIMEOFFSET:
                if (array_key_exists('db_column_datetimeoffset_precision', $this->date)) {
                    return $this->date['db_column_datetimeoffset_precision'];
                }
                break;
            case Enum\DB\ColumnType::BIGINT_DATE:
                if (array_key_exists('db_column_bigintdate_precision', $this->date)) {
                    return $this->date['db_column_bigintdate_precision'];
                }
                break;
            case Enum\DB\ColumnType::TIME:
                if (array_key_exists('db_column_time_precision', $this->date)) {
                    return $this->date['db_column_time_precision'];
                }
                break;
            default:
                return 0;
        }
        return 0;
    }

    /**
     * @return DateTimeZone
     */
    public function getDBTimeZone(): DateTimeZone
    {
        return $this->date['db_timezone'];
    }

    /**
     * @return DateTimeZone
     */
    public function getClientTimeZone(): DateTimeZone
    {
        return $this->date['client_timezone'];
    }

    /**
     * @return string
     * @deprecated
     */
    public function getDBDateTime(): string
    {
        trigger_error(__METHOD__.": Deprecated method called in: ".get_called_class(), E_USER_DEPRECATED);
        $tmp = new DateTime('now', $this->date['db_timezone']);
        return $tmp->format($this->date['db_column_datetime2_format']);
    }

    /**
     * Here the date/time format for php class DateTime is defined.
     *
     * @return string
     * @deprecated
     */
    public function getDBTimeFormat(): string
    {
        trigger_error(__METHOD__.": Deprecated method called in: ".get_called_class(), E_USER_DEPRECATED);
        return 'Y-m-d H:i:s.u';
    }

    public function getClientTimeFormat(): void
    {
        trigger_error(__METHOD__.": Deprecated method called in: ".get_called_class(), E_USER_DEPRECATED);
    }

    public function getClientDateTimeFormat(): string
    {
        trigger_error(__METHOD__.": Deprecated method called in: ".get_called_class(), E_USER_DEPRECATED);
        return $this->date['clientDateTimeFormat'];
    }

    public function getClientDateFormat(): void
    {
        trigger_error(__METHOD__.": Deprecated method called in: ".get_called_class(), E_USER_DEPRECATED);
    }

    public function addTab(Tab|TabNew ...$tabs)
    {
        $this->tabs->addTab(...$tabs);
    }

    /**
     * @API this method is called in bs_locale.php
     * @param string $locale
     * @return array
     * @internal
     */
    public function getLocaleForAllObjects(string $locale): array
    {
        $result = [];
        if ($this->sessionLocale->isSupportedLocale($locale)) {
            $this->sessionLocale->setUserSelectedLocale($locale);
            $result = $this->tabs->getLocaleForAllTabs();
        }
        $result['state'] = 0;
        return $result;
    }

    public function removeTab(ID\ID $id): bool
    {
        return $this->tabs->removeTab($id);
    }

    public function getNavigationArray(bool $debug, ?string $dhtmlxCssImagePath): array
    {
        $result           = $this->tabs->getTabContent();
        $result['locale'] = $this->sessionLocale->getInterfaceLocale();
        $result['debug']  = $debug;
        if ($dhtmlxCssImagePath !== null) {
            $result['dhtmlxCssImgPath'] = $dhtmlxCssImagePath;
        }
        $result['state'] = 2;
        return $result;
    }

    public function registerCell(Cell $cell): void
    {
        $cellId = $cell->getEncodedId();
        if (!array_key_exists($cellId, $this->cells)) {
            $this->cells[$cellId] = $cell;
        }
    }

    /**
     * @param string $permission
     * @return int
     */
    public function getPermissionAccessType(string $permission): int
    {
        return ($this->permissions !== null) ? $this->permissions->getPermissionAccessType($permission) : 0;
    }

    public function getPermissionIDArray($permission): array
    {
        return ($this->permissions !== null) ? $this->permissions->getPermissionIDArray($permission) : [];
    }

    /**
     * @param array $locales
     */
    public function setLocales(array $locales): void
    {
        $this->sessionLocale->setSupportedApplicationLocales($locales);
    }

    public function getSizeData(string $name): array
    {
        if (array_key_exists($name, $this->cell_size)) {
            return $this->cell_size[$name];
        }
        return [];
    }

    public function setSavedCellSize(string $cell, string $type, int $dimension): void
    {
        $this->cell_size[$cell][$type] = $dimension;
    }

    public function setSavedCellCollapse(string $cell): void
    {
        $this->cell_size[$cell][Cell::COLLAPSED] = true;
    }
}
