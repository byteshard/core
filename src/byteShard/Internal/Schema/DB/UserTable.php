<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Schema\DB;

use byteShard\Enum\DB\ColumnType;
use byteShard\Environment;

/**
 * Class UserTable
 * @package byteShard\Internal\Schema\DB
 */
class UserTable
{

    public function __set(string $name, mixed $value)
    {
        trigger_error('accessing '.$name.' directly is deprecated. Please use the respective setter', E_USER_DEPRECATED);
        match ($name) {
            'tablename'                                => $this->setTableName($value),
            'fieldname_Username'                       => $this->setFieldNameUsername($value),
            'fieldtype_Username'                       => $this->setFieldTypeUsername($value),
            'fieldname_User_ID'                        => $this->setFieldNameUserId($value),
            'fieldtype_User_ID'                        => $this->setFieldTypeUserId($value),
            'fieldname_accessControlTarget'            => $this->setFieldNameAccessControlTarget($value),
            'fieldname_authenticationTarget'           => $this->setFieldNameAuthenticationTarget($value),
            'fieldname_grantLogin'                     => $this->setFieldNameGrantLogin($value),
            'fieldname_serviceAccount'                 => $this->setFieldNameServiceAccount($value),
            'fieldname_lastTab'                        => $this->setFieldNameLastTab($value),
            'fieldname_lastLogin'                      => $this->setFieldNameLastLogin($value),
            'fieldname_loginCount'                     => $this->setFieldNameLoginCount($value),
            'fieldname_localPassword'                  => $this->setFieldNameLocalPassword($value),
            'fieldname_localPassword_Expires'          => $this->setFieldNameLocalPasswordExpires($value),
            'fieldname_localPassword_LastChange'       => $this->setFieldNameLocalPasswordLastChange($value),
            'fieldname_localPassword_ExpiresAfterDays' => $this->setFieldNameLocalPasswordExpiresAfterDays($value),
        };
    }

    public function __get(string $name)
    {
        trigger_error('accessing '.$name.' directly is deprecated. Please use the respective getter', E_USER_DEPRECATED);
        return match ($name) {
            'tablename'                                => $this->getTableName(),
            'fieldname_Username'                       => $this->getFieldNameUsername(),
            'fieldtype_Username'                       => $this->getFieldTypeUsername(),
            'fieldname_User_ID'                        => $this->getFieldNameUserId(),
            'fieldtype_User_ID'                        => $this->getFieldTypeUserId(),
            'fieldname_accessControlTarget'            => $this->getFieldNameAccessControlTarget(),
            'fieldname_authenticationTarget'           => $this->getFieldNameAuthenticationTarget(),
            'fieldname_grantLogin'                     => $this->getFieldNameGrantLogin(),
            'fieldname_serviceAccount'                 => $this->getFieldNameServiceAccount(),
            'fieldname_lastTab'                        => $this->getFieldNameLastTab(),
            'fieldname_lastLogin'                      => $this->getFieldNameLastLogin(),
            'fieldname_loginCount'                     => $this->getFieldNameLoginCount(),
            'fieldname_localPassword'                  => $this->getFieldNameLocalPassword(),
            'fieldname_localPassword_Expires'          => $this->getFieldNameLocalPasswordExpires(),
            'fieldname_localPassword_LastChange'       => $this->getFieldNameLocalPasswordLastChange(),
            'fieldname_localPassword_ExpiresAfterDays' => $this->getFieldNameLocalPasswordExpiresAfterDays(),
        };
    }

    private string $tableName                              = 'tbl_User';
    private string $fieldNameUsername                      = 'User';
    private string $fieldTypeUsername                      = ColumnType::VARCHAR;
    private string $fieldNameUserId                        = 'User_ID';
    private string $fieldTypeUserId                        = ColumnType::INT;
    private string $fieldNameAccessControlTarget           = '';
    private string $fieldNameAuthenticationTarget          = 'AuthTarget';
    private string $fieldNameGrantLogin                    = 'GrantLogin';
    private string $fieldNameServiceAccount                = 'ServiceAccount';
    private string $fieldNameLastTab                       = 'LastTab';
    private string $fieldNameLastLogin                     = '';
    private string $fieldNameLoginCount                    = '';
    private string $fieldNameLocalPassword                 = 'Password';
    private string $fieldNameLocalPasswordExpires          = '';
    private string $fieldNameLocalPasswordLastChange       = '';
    private string $fieldNameLocalPasswordExpiresAfterDays = '';


    public function __construct()
    {
        global $dbDriver;
        switch ($dbDriver) {
            case Environment::DRIVER_PGSQL_PDO:
                $this->tableName                              = 'tbl_user';
                $this->fieldNameUsername                      = 'username';
                $this->fieldNameUserId                        = 'user_id';
                $this->fieldNameAuthenticationTarget          = 'authtarget';
                $this->fieldNameGrantLogin                    = 'grantlogin';
                $this->fieldNameServiceAccount                = 'serviceaccount';
                $this->fieldNameLastTab                       = 'lasttab';
                $this->fieldNameLocalPassword                 = 'password';
        }
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     * @return UserTable
     */
    public function setTableName(string $tableName): UserTable
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameUsername(): string
    {
        return $this->fieldNameUsername;
    }

    /**
     * @param string $fieldNameUsername
     * @return UserTable
     */
    public function setFieldNameUsername(string $fieldNameUsername): UserTable
    {
        $this->fieldNameUsername = $fieldNameUsername;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldTypeUsername(): string
    {
        return $this->fieldTypeUsername;
    }

    /**
     * @param string $fieldTypeUsername
     * @return UserTable
     */
    public function setFieldTypeUsername(string $fieldTypeUsername): UserTable
    {
        $this->fieldTypeUsername = $fieldTypeUsername;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameUserId(): string
    {
        return $this->fieldNameUserId;
    }

    /**
     * @param string $fieldNameUserId
     * @return UserTable
     */
    public function setFieldNameUserId(string $fieldNameUserId): UserTable
    {
        $this->fieldNameUserId = $fieldNameUserId;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldTypeUserId(): string
    {
        return $this->fieldTypeUserId;
    }

    /**
     * @param string $fieldTypeUserId
     * @return UserTable
     */
    public function setFieldTypeUserId(string $fieldTypeUserId): UserTable
    {
        $this->fieldTypeUserId = $fieldTypeUserId;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameAccessControlTarget(): string
    {
        return $this->fieldNameAccessControlTarget;
    }

    /**
     * @param string $fieldNameAccessControlTarget
     * @return UserTable
     */
    public function setFieldNameAccessControlTarget(string $fieldNameAccessControlTarget): UserTable
    {
        $this->fieldNameAccessControlTarget = $fieldNameAccessControlTarget;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameAuthenticationTarget(): string
    {
        return $this->fieldNameAuthenticationTarget;
    }

    /**
     * @param string $fieldNameAuthenticationTarget
     * @return UserTable
     */
    public function setFieldNameAuthenticationTarget(string $fieldNameAuthenticationTarget): UserTable
    {
        $this->fieldNameAuthenticationTarget = $fieldNameAuthenticationTarget;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameGrantLogin(): string
    {
        return $this->fieldNameGrantLogin;
    }

    /**
     * @param string $fieldNameGrantLogin
     * @return UserTable
     */
    public function setFieldNameGrantLogin(string $fieldNameGrantLogin): UserTable
    {
        $this->fieldNameGrantLogin = $fieldNameGrantLogin;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameServiceAccount(): string
    {
        return $this->fieldNameServiceAccount;
    }

    /**
     * @param string $fieldNameServiceAccount
     * @return UserTable
     */
    public function setFieldNameServiceAccount(string $fieldNameServiceAccount): UserTable
    {
        $this->fieldNameServiceAccount = $fieldNameServiceAccount;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameLastTab(): string
    {
        return $this->fieldNameLastTab;
    }

    /**
     * @param string $fieldNameLastTab
     * @return UserTable
     */
    public function setFieldNameLastTab(string $fieldNameLastTab): UserTable
    {
        $this->fieldNameLastTab = $fieldNameLastTab;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameLastLogin(): string
    {
        return $this->fieldNameLastLogin;
    }

    /**
     * @param string $fieldNameLastLogin
     * @return UserTable
     */
    public function setFieldNameLastLogin(string $fieldNameLastLogin): UserTable
    {
        $this->fieldNameLastLogin = $fieldNameLastLogin;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameLoginCount(): string
    {
        return $this->fieldNameLoginCount;
    }

    /**
     * @param string $fieldNameLoginCount
     * @return UserTable
     */
    public function setFieldNameLoginCount(string $fieldNameLoginCount): UserTable
    {
        $this->fieldNameLoginCount = $fieldNameLoginCount;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameLocalPassword(): string
    {
        return $this->fieldNameLocalPassword;
    }

    /**
     * @param string $fieldNameLocalPassword
     * @return UserTable
     */
    public function setFieldNameLocalPassword(string $fieldNameLocalPassword): UserTable
    {
        $this->fieldNameLocalPassword = $fieldNameLocalPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameLocalPasswordExpires(): string
    {
        return $this->fieldNameLocalPasswordExpires;
    }

    /**
     * @param string $fieldNameLocalPasswordExpires
     * @return UserTable
     */
    public function setFieldNameLocalPasswordExpires(string $fieldNameLocalPasswordExpires): UserTable
    {
        $this->fieldNameLocalPasswordExpires = $fieldNameLocalPasswordExpires;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameLocalPasswordLastChange(): string
    {
        return $this->fieldNameLocalPasswordLastChange;
    }

    /**
     * @param string $fieldNameLocalPasswordLastChange
     * @return UserTable
     */
    public function setFieldNameLocalPasswordLastChange(string $fieldNameLocalPasswordLastChange): UserTable
    {
        $this->fieldNameLocalPasswordLastChange = $fieldNameLocalPasswordLastChange;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameLocalPasswordExpiresAfterDays(): string
    {
        return $this->fieldNameLocalPasswordExpiresAfterDays;
    }

    /**
     * @param string $fieldNameLocalPasswordExpiresAfterDays
     * @return UserTable
     */
    public function setFieldNameLocalPasswordExpiresAfterDays(string $fieldNameLocalPasswordExpiresAfterDays): UserTable
    {
        $this->fieldNameLocalPasswordExpiresAfterDays = $fieldNameLocalPasswordExpiresAfterDays;
        return $this;
    }


    public function getLastTabQuery(string|int $user_id): string
    {
        if (isset($this->fieldNameLastTab, $this->tableName, $this->fieldNameUserId, $this->fieldTypeUserId)) {
            return 'SELECT '.$this->fieldNameLastTab.' FROM '.$this->tableName.' WHERE '.$this->fieldNameUserId.'='.$this->getEscapedUserID($user_id);
        }
        return '';
    }

    public function getEscapedUserID(string|int $userId): string
    {
        return ColumnType::is_numeric($this->fieldTypeUserId) === true ? (string)$userId : "'".$userId."'";
    }
}
