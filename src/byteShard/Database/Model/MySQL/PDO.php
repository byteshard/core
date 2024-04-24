<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Database\Model\MySQL;

use byteShard\Cell;
use byteShard\Database;
use byteShard\DataModelInterface;
use byteShard\Exception;
use byteShard\Internal\Debug;
use byteShard\Internal\Schema\DB\UserTable;
use byteShard\Settings;

class PDO implements DataModelInterface
{
    private string $tableName                              = 'tbl_User';
    private string $fieldNameLastTab                       = 'LastTab';
    private string $fieldNameUsername                      = 'User';
    private string $fieldNameUserId                        = 'User_ID';
    private string $fieldNamePassword                      = 'Password';
    private string $fieldNameLocalPasswordExpires          = '';
    private string $fieldNameLocalPasswordExpiresAfterDays = '';
    private string $fieldNameLocalPasswordLastChange       = '';
    private string $fieldNameServiceAccount                = 'ServiceAccount';
    private string $fieldNameLastLogin                     = '';
    private string $fieldNameLoginCount                    = '';
    private string $fieldNameGrantLogin                    = 'GrantLogin';


    public function setUserTableSchema(UserTable $schema): void
    {
        $this->tableName                              = $schema->getTableName();
        $this->fieldNameLastTab                       = $schema->getFieldNameLastTab();
        $this->fieldNameUsername                      = $schema->getFieldNameUsername();
        $this->fieldNameUserId                        = $schema->getFieldNameUserId();
        $this->fieldNamePassword                      = $schema->getFieldNameLocalPassword();
        $this->fieldNameLocalPasswordExpires          = $schema->getFieldNameLocalPasswordExpires();
        $this->fieldNameLocalPasswordExpiresAfterDays = $schema->getFieldNameLocalPasswordExpiresAfterDays();
        $this->fieldNameLocalPasswordLastChange       = $schema->getFieldNameLocalPasswordLastChange();
        $this->fieldNameServiceAccount                = $schema->getFieldNameServiceAccount();
        $this->fieldNameLastLogin                     = $schema->getFieldNameLastLogin();
        $this->fieldNameLoginCount                    = $schema->getFieldNameLoginCount();
        $this->fieldNameGrantLogin                    = $schema->getFieldNameGrantLogin();
    }

    public function getLastTab(int $userId): string
    {
        try {
            $record = Database::getSingle('SELECT '.$this->fieldNameLastTab.' AS lasttab FROM '.$this->tableName.' WHERE '.$this->fieldNameUserId.'=:userId', ['userId' => $userId]);
        } catch (\Exception) {
            return '';
        }
        if ($record !== null) {
            return $record->lasttab ?? '';
        }
        return '';
    }

    public function getPasswordHash(string $username): ?string
    {
        try {
            $record = Database::getSingle('SELECT '.$this->fieldNamePassword.' FROM '.$this->tableName.' WHERE '.$this->fieldNameUsername.'=:username', ['username' => $username]);
        } catch (\Exception $e) {
            Debug::error($e->getMessage());
            return null;
        }
        if ($record === null) {
            return null;
        }
        return $record->{$this->fieldNamePassword} ?? '';
    }

    // return null if password never expires
    public function getPasswordExpiration(string $username): ?object
    {
        if ($this->fieldNameLocalPasswordExpires === '') {
            return null;
        }
        if (empty($this->fieldNameLocalPasswordExpiresAfterDays) || empty($this->fieldNameLocalPasswordLastChange)) {
            throw new Exception('Password is supposed to expire but no columns for lastChange or expiresAfterDays have been defined');
        }
        $columns = [
            $this->fieldNameLocalPasswordExpires.' AS expires',
            $this->fieldNameLocalPasswordExpiresAfterDays.' AS expiresAfterDays',
            $this->fieldNameLocalPasswordLastChange.' AS lastChange'
        ];
        return Database::getSingle('SELECT '.implode(', ', $columns).' FROM '.$this->tableName.' WHERE '.$this->fieldNameUsername.'=:username', ['username' => $username]);
    }

    /**
     * @throws Exception
     */
    public function updatePasswordHash(string $username, string $hash): void
    {
        Database::update('UPDATE '.$this->tableName.' SET '.$this->fieldNamePassword.'=:hash WHERE '.$this->fieldNameUsername.'=:username', ['hash' => $hash, 'username' => $username]);
    }

    /** @throws Exception */
    public function setLastTab(int $userId, string $lastTab): bool
    {
        $table         = $this->tableName;
        $lastTabColumn = $this->fieldNameLastTab;
        $userIdColumn  = $this->fieldNameUserId;
        $rs            = Database::getSingle('SELECT '.$lastTabColumn.' as lasttab FROM '.$table.' WHERE '.$userIdColumn.'=:userId', ['userId' => $userId]);
        if ($rs !== null && ($rs->lasttab === null || $rs->lasttab !== $lastTab)) {
            if (Settings::logTabChangeAndPopup() === true) {
                Debug::notice('[Tab] '.$lastTab);
            }
            Database::update('UPDATE '.$table.' SET '.$lastTabColumn.'=:lastTab WHERE '.$userIdColumn.'=:userId', ['lastTab' => $lastTab, 'userId' => $userId]);
        }
        return true;
    }

    /** @throws Exception */
    public function storeUserSetting(string $tabName, string $cellName, string $type, string $item, int $userId, $value): bool
    {
        $fields          = [
            'UserId' => $userId,
            'Tab'    => $tabName,
            'Cell'   => $cellName,
            'Type'   => $type,
            'Item'   => $item
        ];
        $query           = 'SELECT count(1) AS Cnt FROM tbl_User_Settings WHERE User_id=:UserId AND Tab=:Tab AND Cell=:Cell AND Type=:Type AND Item=:Item';
        $result          = Database::getSingle($query, $fields);
        $fields['Value'] = $value;
        if ($result === null) {
            $query = 'INSERT INTO tbl_User_Settings (User_ID, Tab, Cell, Type, Item, Value) VALUES (:UserId, :Tab, :Cell, :Type, :Item, :Value)';
            Database::insert($query, $fields);
        } else {
            $query = 'UPDATE tbl_User_Settings SET Value=:Value WHERE User_ID=:UserId AND Tab=:Tab AND Cell=:Cell AND Type=:Type AND Item=:Item';
            Database::update($query, $fields);
        }
        return true;
    }

    /** @throws Exception */
    public function deleteUserSetting(string $tabName, string $cellName, string $type, string $item, int $userId): bool
    {
        Database::delete('DELETE FROM tbl_User_Settings WHERE User_ID=:UserId AND Tab=:Tab AND Cell=:Cell AND Type=:Type AND Item=:Item', [
            'UserId' => $userId,
            'Tab'    => $tabName,
            'Cell'   => $cellName,
            'Type'   => $type,
            'Item'   => $item
        ]);
        return true;
    }

    /** @throws Exception */
    public function isServiceAccount(int $userId): bool
    {
        $record = Database::getSingle('SELECT '.$this->fieldNameServiceAccount.' FROM '.$this->tableName.' WHERE '.$this->fieldNameUserId.'=:userId', [
            'userId' => $userId
        ]);
        return $record !== null && isset($record->{$this->fieldNameServiceAccount}) && (bool)$record->{$this->fieldNameServiceAccount} === true;
    }

    /** @throws Exception */
    public function getCellSize(int $userId): array
    {
        return Database::getArray("SELECT Tab, Cell, Type, Value FROM tbl_User_Settings WHERE Type IN ('".Cell::HEIGHT."', '".Cell::WIDTH."', '".Cell::COLLAPSED."') AND User_ID=:userId", ['userId' => $userId]);
    }

    /** @throws Exception */
    public function successfulLoginCallback(int $userId): bool
    {
        $set        = [];
        $parameters = [];
        if ($this->fieldNameLastLogin !== '') {
            $set[]                   = $this->fieldNameLastLogin.'=:lastLogin';
            $parameters['lastLogin'] = date('YmdHis', time());
        }
        if ($this->fieldNameLoginCount !== '') {
            $set[] = $this->fieldNameLoginCount.'='.$this->fieldNameLoginCount.' + 1';
        }
        if (!empty($set)) {
            $parameters['userId'] = $userId;
            Database::update('UPDATE '.$this->tableName.' SET '.implode(', ', $set).' WHERE '.$this->fieldNameUserId.'=:userId', $parameters);
        }
        return true;
    }

    /** @throws Exception */
    public function checkGrantLogin(int $userId): bool
    {
        $record = Database::getSingle('SELECT '.$this->fieldNameGrantLogin.' FROM '.$this->tableName.' WHERE '.$this->fieldNameUserId.'=:userId', ['userId' => $userId]);
        if ($record === null) {
            Debug::info(__METHOD__.': user not found');
            return false;
        }
        return (bool)$record->{$this->fieldNameGrantLogin};
    }

    /** @throws Exception|\Exception */
    public function getUserId(string $username): ?int
    {
        $tmp = Database::getSingle('SELECT '.$this->fieldNameUserId.' FROM '.$this->tableName.' WHERE '.$this->fieldNameUsername.'=:userName', ['userName' => $username]);
        if ($tmp === null) {
            Debug::info(__METHOD__.': User not found');
            return null;
        } else {
            return (int)$tmp->{$this->fieldNameUserId};
        }
    }
}
