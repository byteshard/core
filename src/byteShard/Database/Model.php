<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Database;

use byteShard\Authentication\Enum\Target;
use byteShard\Cell;
use byteShard\Database;
use byteShard\Database\Enum\ConnectionType;
use byteShard\DataModelInterface;
use byteShard\Enum\AccessControlTarget;
use byteShard\Enum\DB\ColumnType;
use byteShard\Environment;
use byteShard\Exception;
use byteShard\Internal\Debug;
use byteShard\Internal\Schema\DB\UserTable;
use byteShard\Session;
use byteShard\Settings;

class Model implements DataModelInterface
{
    /** @throws Exception */
    public function getLastTab(int $userId, UserTable $schema): string
    {
        if ($schema->getFieldNameLastTab() !== null && $schema->getTableName() !== null && $schema->getFieldNameUserId() !== null && $schema->getFieldTypeUserId() !== null) {
            global $dbDriver;
            switch ($dbDriver) {
                case Environment::DRIVER_PGSQL_PDO:
                case Environment::DRIVER_MYSQL_PDO:
                    $record = Database::getSingle('SELECT '.$schema->getFieldNameLastTab().' AS lasttab FROM '.$schema->getTableName().' WHERE '.$schema->getFieldNameUserId().'=:userId', ['userId' => $userId]);
                    if ($record !== null) {
                        return $record->lasttab ?? '';
                    }
                    break;
                default:
                    $record = Database::getSingle('SELECT '.$schema->getFieldNameLastTab().' AS lasttab FROM '.$schema->getTableName().' WHERE '.$schema->getFieldNameUserId().'='.$schema->getEscapedUserID($userId));
                    if ($record !== null) {
                        return $record->lasttab ?? '';
                    }
                    break;
            }
        }
        return '';
    }

    /** @throws Exception */
    public function setLastTab(int $userId, string $lastTab, UserTable $schema): bool
    {
        if ($schema->getFieldNameLastTab() !== null && $schema->getTableName() !== null && $schema->getFieldNameUserId() !== null && $schema->getFieldTypeUserId() !== null) {
            $table         = $schema->getTableName();
            $lastTabColumn = $schema->getFieldNameLastTab();
            $userIdColumn  = $schema->getFieldNameUserId();
            global $dbDriver;
            switch ($dbDriver) {
                case Environment::DRIVER_PGSQL_PDO:
                    $rs = Database::getSingle(strtolower('SELECT '.$lastTabColumn.' as lasttab FROM '.$table.' WHERE '.$userIdColumn).'=:userId', ['userId' => $userId]);
                    if ($rs !== null && ($rs->lasttab === null || $rs->lasttab !== $lastTab)) {
                        if (Settings::logTabChangeAndPopup() === true) {
                            Debug::notice('[Tab] '.$lastTab);
                        }
                        Database::update('UPDATE '.$table.' SET '.strtolower($lastTabColumn).'=:lastTab WHERE '.strtolower($userIdColumn).'=:userId', ['lastTab' => $lastTab, 'userId' => $userId]);
                    }
                    break;
                case Environment::DRIVER_MYSQL_PDO:
                    $rs = Database::getSingle('SELECT '.$lastTabColumn.' as lasttab FROM '.$table.' WHERE '.$userIdColumn.'=:userId', ['userId' => $userId]);
                    if ($rs !== null && ($rs->lasttab === null || $rs->lasttab !== $lastTab)) {
                        if (Settings::logTabChangeAndPopup() === true) {
                            Debug::notice('[Tab] '.$lastTab);
                        }
                        Database::update('UPDATE '.$table.' SET '.$lastTabColumn.'=:lastTab WHERE '.$userIdColumn.'=:userId', ['lastTab' => $lastTab, 'userId' => $userId]);
                    }
                    break;
                default:
                    $query = 'SELECT '.$lastTabColumn.' FROM '.$table.' WHERE '.$userIdColumn.'='.$schema->getEscapedUserID($userId);
                    $rs    = Database::getRecordset($cn = Database::getConnection(ConnectionType::WRITE));
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
                    break;
            }
        }
        return true;
    }

    /** @throws Exception */
    public function storeUserSetting(string $tabName, string $cellName, string $type, string $item, int $userId, $value): bool
    {
        global $dbDriver;
        switch ($dbDriver) {
            case Environment::DRIVER_PGSQL_PDO:
            case Environment::DRIVER_MYSQL_PDO:
                $fields          = [
                    'UserId' => $userId,
                    'Tab'    => $tabName,
                    'Cell'   => $cellName,
                    'Type'   => $type,
                    'Item'   => $item
                ];
                $query           = /** @lang PostgreSQL */
                    'SELECT count(1) AS Cnt FROM tbl_User_Settings WHERE User_id=:UserId AND Tab=:Tab AND Cell=:Cell AND Type=:Type AND Item=:Item';
                $result          = Database::getSingle($query, $fields);
                $fields['Value'] = $value;
                if ($result === null) {
                    $query = /** @lang PostgreSQL */
                        'INSERT INTO tbl_User_Settings (User_ID, Tab, Cell, Type, Item, Value) VALUES (:UserId, :Tab, :Cell, :Type, :Item, :Value)';
                    Database::insert($query, $fields);
                } else {
                    $query = /** @lang PostgreSQL */
                        'UPDATE tbl_User_Settings SET Value=:Value WHERE User_ID=:UserId AND Tab=:Tab AND Cell=:Cell AND Type=:Type AND Item=:Item';
                    Database::update($query, $fields);
                }
                break;
            default:
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
                break;
        }
        return true;
    }

    /** @throws Exception */
    public function deleteUserSetting(string $tabName, string $cellName, string $type, string $item, int $userId): bool
    {
        global $dbDriver;

        switch ($dbDriver) {
            case Environment::DRIVER_PGSQL_PDO:
            case Environment::DRIVER_MYSQL_PDO:
                $fields = [
                    'UserId' => $userId,
                    'Tab'    => $tabName,
                    'Cell'   => $cellName,
                    'Type'   => $type,
                    'Item'   => $item
                ];
                /** @noinspection SqlNoDataSourceInspection SqlDialectInspection */
                $query = 'DELETE FROM tbl_User_Settings WHERE User_ID=:UserId AND Tab=:Tab AND Cell=:Cell AND Type=:Type AND Item=:Item';
                Database::delete($query, $fields);
                break;
            default:
                $rs = Database::getRecordset($cn = Database::getConnection(ConnectionType::WRITE));
                /** @noinspection SqlNoDataSourceInspection SqlDialectInspection */
                $rs->open('SELECT Value FROM tbl_User_Settings WHERE User_ID='.$userId." AND Tab='".$tabName."' AND Cell='".$cellName."' AND Type='".$type."' AND Item='".$item."'");
                if ($rs->recordcount() === 1) {
                    $rs->delete();
                }
                $rs->close();
                $cn->disconnect();
                break;
        }
        return true;
    }

    /** @throws Exception */
    public function isServiceAccount(int $userId, UserTable $schema = null): bool
    {
        global $dbDriver;
        $record = match ($dbDriver) {
            Environment::DRIVER_PGSQL_PDO,
            Environment::DRIVER_MYSQL_PDO => Database::getSingle('SELECT '.$schema->getFieldNameServiceAccount().' FROM '.$schema->getTableName().'  WHERE '.$schema->getFieldNameUserId().'=:userId', [
                'userId' => $userId
            ]),
            default                       => Database::getSingle('SELECT '.$schema->getFieldNameServiceAccount().' FROM '.$schema->getTableName().' WHERE '.$schema->getFieldNameUserId().'='.((ColumnType::is_string($schema->getFieldTypeUserIdEnum()) === true) ? "'".$userId."'" : $userId)),
        };
        return $record !== null && isset($record->{$schema->getFieldNameServiceAccount()}) && (bool)$record->{$schema->getFieldNameServiceAccount()} === true;
    }

    /** @throws Exception */
    public function getCellSize(int $userId): array
    {
        global $dbDriver;
        /** @noinspection SqlNoDataSourceInspection SqlDialectInspection */
        return match ($dbDriver) {
            Environment::DRIVER_PGSQL_PDO,
            Environment::DRIVER_MYSQL_PDO => Database::getArray("SELECT Tab, Cell, Type, Value FROM tbl_User_Settings WHERE Type IN ('".Cell::HEIGHT."', '".Cell::WIDTH."', '".Cell::COLLAPSED."') AND User_ID=:userId", ['userId' => $userId]),
            default                       => Database::getArray("SELECT Tab, Cell, Type, Value FROM tbl_User_Settings WHERE Type IN ('".Cell::HEIGHT."', '".Cell::WIDTH."', '".Cell::COLLAPSED."') AND User_ID='".Session::getUserId()."'"),
        };
    }

    /** @throws Exception */
    public function successfulLoginCallback(int $userId, UserTable $schema = null): bool
    {
        $fields = [];
        if ($schema->getFieldNameLastLogin() !== '') {
            $fields[] = $schema->getFieldNameLastLogin();
        }
        if ($schema->getFieldNameLoginCount() !== '') {
            $fields[] = $schema->getFieldNameLoginCount();
        }
        if (!empty($fields)) {
            global $dbDriver;
            switch ($dbDriver) {
                case Environment::DRIVER_MYSQL_PDO:
                case Environment::DRIVER_PGSQL_PDO:
                    $set        = [];
                    $parameters = [];
                    if ($schema->getFieldNameLastLogin() !== '') {
                        $set[]                                        = $schema->getFieldNameLastLogin().'=:'.$schema->getFieldNameLastLogin();
                        $parameters[$schema->getFieldNameLastLogin()] = date('YmdHis', time());
                    }
                    if ($schema->getFieldNameLoginCount() !== '') {
                        $set[] = $schema->getFieldNameLoginCount().'='.$schema->getFieldNameLoginCount().' + 1';
                    }
                    $parameters['userId'] = $userId;
                    Database::update('UPDATE '.$schema->getTableName().' SET '.implode(', ', $set).' WHERE '.$schema->getFieldNameUserId().'=:userId', $parameters);
                    break;
                default:
                    $rs = Database::getRecordset($cn = Database::getConnection(ConnectionType::WRITE));
                    $rs->open('SELECT '.implode(',', $fields).' FROM '.$schema->getTableName().' WHERE '.$schema->getFieldNameUserId().'='.$userId);
                    if ($rs->recordcount() === 1) {
                        if ($schema->getFieldNameLastLogin() !== null) {
                            $rs->fields[$schema->getFieldNameLastLogin()] = date('YmdHis', time());
                        }
                        if ($schema->getFieldNameLoginCount() !== null) {
                            if ($rs->fields[$schema->getFieldNameLoginCount()] === null) {
                                $rs->fields[$schema->getFieldNameLoginCount()] = 1;
                            } else {
                                $rs->fields[$schema->getFieldNameLoginCount()] += 1;
                            }
                        }
                        $rs->update();
                    }
                    $rs->close();
                    $cn->disconnect();
                    break;
            }
        }
        return true;
    }

    /** @throws Exception */
    public function checkGrantLogin(int|string $userId, UserTable $schema): bool
    {
        global $dbDriver;
        switch ($dbDriver) {
            case Environment::DRIVER_MYSQL_PDO:
            case Environment::DRIVER_PGSQL_PDO:
                $grantLogin = strtolower($schema->getFieldNameGrantLogin());
                $query      = 'SELECT '.$grantLogin.' FROM '.$schema->getTableName().' WHERE '.$schema->getFieldNameUserId().'=:UserId';
                $tmp        = Database::getSingle($query, ['UserId' => $userId]);
                break;
            default:
                $grantLogin = $schema->getFieldNameGrantLogin();
                $query      = 'SELECT '.$schema->getFieldNameGrantLogin().' FROM '.$schema->getTableName().' WHERE '.$schema->getFieldNameUserId().'='.(ColumnType::is_string($schema->getFieldTypeUserIdEnum()) === true ? "'".$userId."'" : $userId);
                $tmp        = Database::getSingle($query);
                break;
        }
        if ($tmp === null) {
            Debug::info(__METHOD__.': user not found');
            return false;
        }
        return (bool)$tmp->{$grantLogin};
    }

    /** @throws Exception|\Exception */
    public function checkServiceAccount(int|string $userId, UserTable $schema): bool
    {
        global $dbDriver;
        $tmp = match ($dbDriver) {
            Environment::DRIVER_MYSQL_PDO,
            Environment::DRIVER_PGSQL_PDO => Database::getSingle('SELECT '.$schema->getFieldNameServiceAccount().' FROM '.$schema->getTableName().' WHERE '.$schema->getFieldNameUserId().':=userId', ['userId' => $userId]),
            default                       => Database::getSingle('SELECT '.$schema->getFieldNameServiceAccount().' FROM '.$schema->getTableName().' WHERE '.$schema->getFieldNameUserId().'='.(ColumnType::is_string($schema->getFieldTypeUserIdEnum()) === true ? "'".$userId."'" : $userId)),
        };
        if ($tmp === null) {
            Debug::info(__METHOD__.': user not found');
            return false;
        }
        return (bool)$tmp->{$schema->getFieldNameServiceAccount()};
    }

    /** @throws Exception|\Exception */
    public function getAccessControlTarget(int|string $userId, UserTable $schema): ?AccessControlTarget
    {
        if ($schema->getFieldNameAccessControlTarget() !== null) {
            global $dbDriver;
            $tmp = match ($dbDriver) {
                Environment::DRIVER_MYSQL_PDO,
                Environment::DRIVER_PGSQL_PDO => Database::getSingle('SELECT '.$schema->getFieldNameAccessControlTarget().' FROM '.$schema->getTableName().' WHERE '.$schema->getFieldNameUserId().':=userId', ['userId' => $userId]),
                default                       => Database::getSingle('SELECT '.$schema->getFieldNameAccessControlTarget().' FROM '.$schema->getTableName().' WHERE '.$schema->getFieldNameUserId().'='.(ColumnType::is_string($schema->getFieldTypeUserIdEnum()) === true ? "'".$userId."'" : $userId)),
            };
            if ($tmp === null) {
                Debug::info(__METHOD__.': user not found');
                return null;
            }
            $target = AccessControlTarget::tryFrom($tmp->{$schema->getFieldNameAccessControlTarget()});
            if ($target === null) {
                Debug::info(__METHOD__.': Value in column "'.$schema->getFieldNameAccessControlTarget().'" table "'.$schema->getTableName().'" must be of Enum::BSAccessControlTarget');
                return null;
            }
            if ($target === AccessControlTarget::ACCESS_CONTROL_DEFINED_ON_DB) {
                // circular reference
                Debug::info(__METHOD__.': Value in column "'.$schema->getFieldNameAccessControlTarget().'" table "'.$schema->getTableName().'" must NOT be '.AccessControlTarget::ACCESS_CONTROL_DEFINED_ON_DB->value);
                return null;
            }
            return $target;
        }
        throw new \Exception(__METHOD__.': Access control target is set to ACCESS_CONTROL_DEFINED_ON_DB but getFieldNameAccessControlTarget is undefined in Schema\DB\UserTable');
    }

    /** @throws Exception|\Exception */
    public function getAuthenticationTarget(int|string $userId, UserTable $schema): ?Target
    {
        if ($schema->getFieldNameAuthenticationTarget() !== null) {
            global $dbDriver;
            $tmp = match ($dbDriver) {
                Environment::DRIVER_MYSQL_PDO,
                Environment::DRIVER_PGSQL_PDO => Database::getSingle('SELECT '.$schema->getFieldNameAuthenticationTarget().' FROM '.$schema->getTableName().' WHERE '.$schema->getFieldNameUserId().':=userId', ['userId' => $userId]),
                default                       => Database::getSingle('SELECT '.$schema->getFieldNameAuthenticationTarget().' FROM '.$schema->getTableName().' WHERE '.$schema->getFieldNameUserId().'='.(ColumnType::is_string($schema->getFieldTypeUserIdEnum()) === true ? "'".$userId."'" : $userId)),
            };
            if ($tmp === null) {
                Debug::info(__METHOD__.': user not found');
                return null;
            }
            $target = Target::tryFrom($tmp->{$schema->getFieldNameAuthenticationTarget()});
            if ($target === null) {
                Debug::info(__METHOD__.': Value in column "'.$schema->getFieldNameAuthenticationTarget().'" table "'.$schema->getTableName().'" must be of Enum::Authentication\Enum\Target');
                return null;
            } elseif ($target === Target::AUTH_TARGET_DEFINED_ON_DB) {
                Debug::info(__METHOD__.': Value in column "'.$schema->getFieldNameAuthenticationTarget().'" table "'.$schema->getTableName().'" must NOT be '.Target::AUTH_TARGET_DEFINED_ON_DB->value);
                return null;
            } else {
                return $target;
            }
        }
        throw new \Exception(__METHOD__.': Authentication Target is set to AUTH_TARGET_DEFINED_ON_DB but getFieldNameAuthenticationTarget is undefined in Schema\DB\UserTable');
    }

    /** @throws Exception|\Exception */
    public function getUserId(string $username, UserTable $schema): ?int
    {
        if ($schema->getFieldNameUsername() !== null && $schema->getFieldTypeUsername() !== null && $schema->getTableName() !== null && $schema->getFieldNameUserId() !== null) {
            global $dbDriver;
            $tmp = match ($dbDriver) {
                Environment::DRIVER_PGSQL_PDO,
                Environment::DRIVER_MYSQL_PDO => Database::getSingle('SELECT '.$schema->getFieldNameUserId().' FROM '.$schema->getTableName().' WHERE '.$schema->getTableName().'.'.$schema->getFieldNameUsername().'=:userName', ['userName' => $username]),
                default                       => Database::getSingle('SELECT '.$schema->getFieldNameUserId().' FROM '.$schema->getTableName().' WHERE '.$schema->getFieldNameUsername().'='.(ColumnType::is_string($schema->getFieldTypeUsernameEnum()) === true ? "'".$username."'" : $username)),
            };
            if ($tmp === null) {
                Debug::info(__METHOD__.': User not found');
                return null;
            } else {
                return (int)$tmp->{$schema->getFieldNameUserId()};
            }
        }
        if ($schema->getFieldNameUsername() === null) {
            throw new \Exception(__METHOD__.': getFieldNameUsername is undefined in Schema\DB\UserTable');
        }
        if ($schema->getFieldNameUsername() === null) {
            throw new \Exception(__METHOD__.': getFieldNameUsername is undefined in Schema\DB\UserTable');
        }
        if ($schema->getFieldNameUserId() === null) {
            throw new \Exception(__METHOD__.': getFieldNameUserId is undefined in Schema\DB\UserTable');
        }
        throw new \Exception(__METHOD__.': getTableName is undefined in Schema\DB\UserTable');
    }
}
