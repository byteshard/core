<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Database\Model;

use byteShard\Cell;
use byteShard\Database;
use byteShard\Database\Enum\ConnectionType;
use byteShard\DataModelInterface;
use byteShard\Enum\DB\ColumnType;
use byteShard\Exception;
use byteShard\Internal\Debug;
use byteShard\Internal\Schema\DB\UserTable;
use byteShard\Session;
use byteShard\Settings;

class DeprecatedModel implements DataModelInterface
{
    private string     $tableName                              = 'tbl_User';
    private string     $fieldNameLastTab                       = 'LastTab';
    private string     $fieldNameUsername                      = 'User';
    private string     $fieldNameUserId                        = 'User_ID';
    private string     $fieldNamePassword                      = 'Password';
    private string     $fieldNameLocalPasswordExpires          = '';
    private string     $fieldNameLocalPasswordExpiresAfterDays = '';
    private string     $fieldNameLocalPasswordLastChange       = '';
    private string     $fieldNameServiceAccount                = 'ServiceAccount';
    private string     $fieldNameLastLogin                     = '';
    private string     $fieldNameLoginCount                    = '';
    private string     $fieldNameGrantLogin                    = 'GrantLogin';
    private ColumnType $fieldTypeUserId                        = ColumnType::INT;

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
        $this->fieldTypeUserId                        = $schema->getFieldTypeUserIdEnum();
    }

    private function getEscapedUserID(int $userId): string
    {
        return ColumnType::is_numeric($this->fieldTypeUserId) === true ? (string)$userId : "'".$userId."'";
    }


    /** @throws Exception */
    public function getLastTab(int $userId): string
    {
        $record = Database::getSingle('SELECT '.$this->fieldNameLastTab.' AS lasttab FROM '.$this->tableName.' WHERE '.$this->fieldNameUserId.'='.$this->getEscapedUserID($userId));
        if ($record !== null) {
            return $record->lasttab ?? '';
        }
        return '';
    }

    public function getPasswordHash(string $username): ?string
    {
        $passwordColumnName = $this->fieldNamePassword;
        $query              = 'SELECT '.$passwordColumnName.' FROM '.$this->tableName.' WHERE '.$this->fieldNameUsername.'=:username';
        $parameters         = ['username' => $username];
        //TODO: implement
        $record = null;
        if ($record === null) {
            return null;
        }
        return $record->{$passwordColumnName} ?? '';
    }

    // return null if password never expires
    public function getPasswordExpiration(string $username): ?object
    {
        $expires = $this->fieldNameLocalPasswordExpires;
        if (empty($expires)) {
            return null;
        }
        $expiresAfterDays = $this->fieldNameLocalPasswordExpiresAfterDays;
        $lastChange       = $this->fieldNameLocalPasswordLastChange;
        if (empty($expiresAfterDays) || empty($lastChange)) {
            throw new Exception('Password is supposed to expire but no columns for lastChange or expiresAfterDays have been defined');
        }
        $columns = [
            $expires.' AS expires',
            $expiresAfterDays.' AS expiresAfterDays',
            $lastChange.' AS lastChange'
        ];
        return Database::getSingle('SELECT '.implode(', ', $columns).' FROM '.$this->tableName.' WHERE '.$this->fieldNameUsername.'=:username', ['username' => $username]);
    }

    /**
     * @throws Exception
     */
    public function updatePasswordHash(string $username, string $hash): void
    {
        // not prepared statement, but should be ok since the user already logged in so the username has to be a valid username and the password is generated by the php hashing function
        $connection = Database::getConnection(ConnectionType::WRITE);
        $connection->execute('UPDATE '.$this->tableName.' SET '.$this->fieldNamePassword.'=\''.$hash.'\' WHERE '.$this->fieldNameUsername.'=\''.$username.'\'');
    }

    /** @throws Exception */
    public function setLastTab(int $userId, string $lastTab): bool
    {
        if ($this->fieldNameLastTab !== null && $this->tableName !== null && $this->fieldNameUserId !== null && $this->fieldTypeUserId !== null) {
            $table         = $this->tableName;
            $lastTabColumn = $this->fieldNameLastTab;
            $userIdColumn  = $this->fieldNameUserId;
            $query         = 'SELECT '.$lastTabColumn.' FROM '.$table.' WHERE '.$userIdColumn.'='.$this->getEscapedUserID($userId);
            $rs            = Database::getRecordset($cn = Database::getConnection(ConnectionType::WRITE));
            $rs->open($query);
            if (($rs->fields[$lastTabColumn] === null || $rs->fields[$lastTabColumn] !== $lastTab) && $rs->recordcount() === 1) {
                if (Settings::logTabChangeAndPopup() === true) {
                    Debug::notice('[Tab] '.$lastTab);
                }
                $rs->fields[$lastTabColumn] = $lastTab;
                $rs->update();
            }
            $rs->close();
            $cn->disconnect();
        }
        return true;
    }

    /** @throws Exception */
    public function storeUserSetting(string $tabName, string $cellName, string $type, string $item, int $userId, $value): bool
    {
        $rs = Database::getRecordset($cn = Database::getConnection(ConnectionType::WRITE));
        /** @noinspection SqlNoDataSourceInspection SqlDialectInspection */
        $rs->open('SELECT User_ID, Tab, Cell, Type, Item, Value FROM tbl_User_Settings WHERE User_ID='.$userId." AND Tab='".str_replace('\\', '\\\\', $tabName)."' AND Cell='".$cellName."' AND Type='".$type."' AND Item='".$item."'");
        if ($rs->recordcount() === 0) {
            $rs->addnew();
            $rs->fields['User_ID'] = $userId;
            $rs->fields['Tab']     = $tabName;
            $rs->fields['Cell']    = $cellName;
            $rs->fields['Type']    = $type;
            $rs->fields['Item']    = $item;
            $rs->fields['Value']   = $value;
            $rs->update();
        } elseif ($rs->recordcount() === 1) {
            $rs->fields['Value'] = $value;
            $rs->update();
        }
        $rs->close();
        $cn->disconnect();
        return true;
    }

    /** @throws Exception */
    public function deleteUserSetting(string $tabName, string $cellName, string $type, string $item, int $userId): bool
    {
        $rs = Database::getRecordset($cn = Database::getConnection(ConnectionType::WRITE));
        /** @noinspection SqlNoDataSourceInspection SqlDialectInspection */
        $rs->open('SELECT Value FROM tbl_User_Settings WHERE User_ID='.$userId." AND Tab='".$tabName."' AND Cell='".$cellName."' AND Type='".$type."' AND Item='".$item."'");
        if ($rs->recordcount() === 1) {
            $rs->delete();
        }
        $rs->close();
        $cn->disconnect();
        return true;
    }

    /** @throws Exception */
    public function isServiceAccount(int $userId): bool
    {
        $record = Database::getSingle('SELECT '.$this->fieldNameServiceAccount.' FROM '.$this->tableName.' WHERE '.$this->fieldNameUserId.'='.(ColumnType::is_string($this->fieldTypeUserId) === true ? "'".$userId."'" : $userId));
        return $record !== null && isset($record->{$this->fieldNameServiceAccount}) && (bool)$record->{$this->fieldNameServiceAccount} === true;
    }

    /** @throws Exception */
    public function getCellSize(int $userId): array
    {
        /** @noinspection SqlNoDataSourceInspection SqlDialectInspection */
        return Database::getArray("SELECT Tab, Cell, Type, Value FROM tbl_User_Settings WHERE Type IN ('".Cell::HEIGHT."', '".Cell::WIDTH."', '".Cell::COLLAPSED."') AND User_ID='".Session::getUserId()."'");
    }

    /** @throws Exception */
    public function successfulLoginCallback(int $userId): bool
    {
        $fields = [];
        if ($this->fieldNameLastLogin !== '') {
            $fields[] = $this->fieldNameLastLogin;
        }
        if ($this->fieldNameLoginCount !== '') {
            $fields[] = $this->fieldNameLoginCount;
        }
        if (!empty($fields)) {
            $rs = Database::getRecordset($cn = Database::getConnection(ConnectionType::WRITE));
            $rs->open('SELECT '.implode(',', $fields).' FROM '.$this->tableName.' WHERE '.$this->fieldNameUserId.'='.$userId);
            if ($rs->recordcount() === 1) {
                if ($this->fieldNameLastLogin !== null) {
                    $rs->fields[$this->fieldNameLastLogin] = date('YmdHis', time());
                }
                if ($this->fieldNameLoginCount !== null) {
                    if ($rs->fields[$this->fieldNameLoginCount] === null) {
                        $rs->fields[$this->fieldNameLoginCount] = 1;
                    } else {
                        $rs->fields[$this->fieldNameLoginCount] += 1;
                    }
                }
                $rs->update();
            }
            $rs->close();
            $cn->disconnect();
        }
        return true;
    }

    /** @throws Exception */
    public function checkGrantLogin(int $userId): bool
    {
        $grantLogin = $this->fieldNameGrantLogin;
        $query      = 'SELECT '.$this->fieldNameGrantLogin.' FROM '.$this->tableName.' WHERE '.$this->fieldNameUserId.'='.(ColumnType::is_string($this->fieldTypeUserId) === true ? "'".$userId."'" : $userId);
        $tmp        = Database::getSingle($query);
        if ($tmp === null) {
            Debug::info(__METHOD__.': user not found');
            return false;
        }
        return (bool)$tmp->{$grantLogin};
    }

    /** @throws Exception|\Exception */
    public function getUserId(string $username): ?int
    {
        $tmp = Database::getSingle('SELECT '.$this->fieldNameUserId.' FROM '.$this->tableName.' WHERE '.$this->fieldNameUsername.'='.(ColumnType::is_string($this->fieldTypeUserId) === true ? "'".$username."'" : $username));
        if ($tmp === null) {
            Debug::info(__METHOD__.': User not found');
            return null;
        } else {
            return (int)$tmp->{$this->fieldNameUserId};
        }
    }
}
