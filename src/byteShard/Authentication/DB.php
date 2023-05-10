<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Authentication;

use byteShard\Environment;
use byteShard\Exception;
use byteShard\Internal\Authentication\AuthenticationInterface;
use byteShard\Internal\Authentication\Struct;
use byteShard\Internal\Debug;
use DateTime;
use byteShard\Internal\Schema;
use byteShard\Enum;
use byteShard\Database;
use byteShard\Authentication\Enum\Action;

/**
 * Class DB
 * @exceptionId 00003
 * @package byteShard\Internal\Authentication
 */
class DB implements AuthenticationInterface
{
    /**
     * @var Schema\DB\UserTable
     */
    private Schema\DB\UserTable $dbSchema;

    private static string $algorithm = PASSWORD_DEFAULT;

    /**
     * @var int
     */
    private static int $cost = 12;

    /**
     * DB constructor.
     * @param Schema\DB\UserTable $dbSchema
     */
    public function __construct(Schema\DB\UserTable $dbSchema)
    {
        $this->dbSchema = $dbSchema;
    }

    /**
     * @param string $username
     * @return bool
     */
    public function checkUsernamePattern(string $username): bool
    {
        return true;
    }

    /**
     * @param Struct\Result $auth
     * @throws Exception
     */
    public function authenticate(Struct\Result $auth): void
    {
        if ($auth->password_cost <= 0) {
            $auth->password_cost = self::$cost;
        }
        if (!empty($this->dbSchema->getFieldNameLocalPassword()) && !empty($this->dbSchema->getTableName()) && !empty($this->dbSchema->getFieldNameUserId()) && !empty($this->dbSchema->getFieldTypeUserId())) {
            $columns[] = $this->dbSchema->getFieldNameLocalPassword();
            if (!empty($this->dbSchema->getFieldNameLocalPasswordExpires())) {
                $columns[] = $this->dbSchema->getFieldNameLocalPasswordExpires();
            }
            if (!empty($this->dbSchema->getFieldNameLocalPasswordExpiresAfterDays())) {
                $columns[] = $this->dbSchema->getFieldNameLocalPasswordExpiresAfterDays();
            }
            if (!empty($this->dbSchema->getFieldNameLocalPasswordLastChange())) {
                $columns[] = $this->dbSchema->getFieldNameLocalPasswordLastChange();
            }
            $tmp = Database::getSingle(
                'SELECT '.implode(', ', $columns).'
                FROM '.$this->dbSchema->getTableName().'
                WHERE '.$this->dbSchema->getFieldNameUserId().'='.((Enum\DB\ColumnType::is_string($this->dbSchema->getFieldTypeUserId()) === true) ? "'".$auth->user_ID."'" : $auth->user_ID)
            );
            // print password_hash($auth->password, self::$algorithm, ['cost' => self::$cost]);
            if ($tmp === null) {
                Debug::info(__METHOD__.': Query did not return a password for user: '.$auth->username);
                return;
            }
            if (!isset($tmp->{$this->dbSchema->getFieldNameLocalPassword()})) {
                Debug::info(__METHOD__.': Column: '.$this->dbSchema->getFieldNameLocalPassword().' not returned by query');
                return;
            }
            if (empty($tmp->{$this->dbSchema->getFieldNameLocalPassword()})) {
                Debug::info(__METHOD__.': Password column empty for user: '.$auth->username);
                return;
            }
            if ($this->passwordVerify($auth, $auth->password ?? '', $tmp->{$this->dbSchema->getFieldNameLocalPassword()}) === true) {
                Debug::debug(__METHOD__.': authentication successful (password ok)');
                $auth->success = true;
                if (isset($tmp->{$this->dbSchema->getFieldNameLocalPasswordExpires()}, $tmp->{$this->dbSchema->getFieldNameLocalPasswordExpiresAfterDays()}) && $tmp->{$this->dbSchema->getFieldNameLocalPasswordExpires()} && is_numeric($tmp->{$this->dbSchema->getFieldNameLocalPasswordExpiresAfterDays()})) {
                    $date = new DateTime(date('Ymd'));
                    $date->modify('-'.$tmp->{$this->dbSchema->getFieldNameLocalPasswordExpiresAfterDays()}.'days');
                    if ($date->format('Ymd') > $tmp->{$this->dbSchema->getFieldNameLocalPasswordLastChange()}) {
                        $auth->action = Action::CHANGE_PASSWORD;
                    }
                }
                return;
            }
            $auth->action = Action::INVALID_CREDENTIALS;
            /*if (empty($this->db_schema->fieldname_localPassword_Expires)) {
                $tmp = Database::getSingle('SELECT '.$this->db_schema->fieldname_localPassword.'
                       FROM '.$this->db_schema->tablename.'
                       WHERE '.$this->db_schema->fieldname_User_ID.'='.((Enum\DB\ColumnType::is_string($this->db_schema->fieldtype_User_ID) === true) ? "'".$auth->user_ID."'" : $auth->user_ID));
                if ($tmp !== null && isset($tmp->{$this->db_schema->fieldname_localPassword}) && !empty($tmp->{$this->db_schema->fieldname_localPassword})) {
                    if ($this->password_verify($auth, $auth->password, $tmp->{$this->db_schema->fieldname_localPassword}) === true) {
                        Debug::debug(__METHOD__.': authentication successful (password ok)');
                        $auth->success = true;
                        return;
                    }
                    Debug::info(__METHOD__.': authentication not successful (password not ok)');
                }
            } else {
                if (!empty($this->db_schema->fieldname_localPassword_ExpiresAfterDays) && !empty($this->db_schema->fieldname_localPassword_LastChange)) {

                    $tmp = Database::getSingle('SELECT '.$this->db_schema->fieldname_localPassword.', '.$this->db_schema->fieldname_localPassword_Expires.', '.$this->db_schema->fieldname_localPassword_ExpiresAfterDays.', '.$this->db_schema->fieldname_localPassword_LastChange.'
               FROM ' .$this->db_schema->tablename. '
               WHERE ' .$this->db_schema->fieldname_User_ID. '=' .((Enum\DB\ColumnType::is_string($this->db_schema->fieldtype_User_ID) === true) ? "'".$auth->user_ID."'" : $auth->user_ID));
                    if ($tmp !== null && isset($tmp->{$this->db_schema->fieldname_localPassword}) && strlen($tmp->{$this->db_schema->fieldname_localPassword}) > 0) {
                        if ($this->password_verify($auth, $auth->password, $tmp->{$this->db_schema->fieldname_localPassword}) === true) {
                            $auth->success = true;
                            if ($tmp->{$this->db_schema->fieldname_localPassword_Expires} == true && is_numeric($tmp->{$this->db_schema->fieldname_localPassword_ExpiresAfterDays})) {
                                $date = new DateTime(date('Ymd'));
                                $date->modify('-'.$tmp->{$this->db_schema->fieldname_localPassword_ExpiresAfterDays}.'days');
                                if ($date->format('Ymd') > $tmp->{$this->db_schema->fieldname_localPassword_LastChange}) {
                                    $auth->action = Action::CHANGE_PASSWORD;
                                }
                            }
                        } else {
                            //TODO: Debug
                        }
                    } else {
                        //TODO: Debug
                    }
                }
            }*/
        } else {
            if (empty($this->dbSchema->getFieldNameLocalPassword())) {
                throw new Exception('Authentication Target defined as DB but no Password column has been defined', 100003001);
            }
            if (empty($this->dbSchema->getTableName())) {
                throw new Exception('No user table defined', 100003002);
            }
            if (empty($this->dbSchema->getFieldNameUserId())) {
                throw new Exception('No user id column defined', 100003003);
            }
            if (empty($this->dbSchema->getFieldTypeUserId())) {
                throw new Exception('No user id column type defined', 100003004);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function changePassword(Struct\Result $auth): Struct\Result
    {
        if ($this->passwordPolicyCheck($auth->newPassword) === false) {
            $auth->action = Action::NEW_PASSWORD_DOESNT_MATCH_POLICY;
            return $auth;
        } else {
            $new_password_hash = self::passwordHash($auth->newPassword, $auth->password_algorithm, $auth->password_cost, $auth->password_salt);
            global $dbDriver;
            switch ($dbDriver) {
                case Environment::DRIVER_MySQL_mysqli:
                    $rs = Database::getRecordset($cn = Database::getConnection(Database\Enum\ConnectionType::WRITE));
                    $rs->open("SELECT ".$this->dbSchema->getFieldNameLocalPassword().", ".$this->dbSchema->getFieldNameLocalPasswordLastChange()." FROM ".$this->dbSchema->getTableName()." WHERE ".$this->dbSchema->getFieldNameUserId()."=".((Enum\DB\ColumnType::is_string($this->dbSchema->getFieldTypeUserId()) === true) ? "'".$auth->user_ID."'" : $auth->user_ID));
                    if ($rs->recordcount() === 1) {
                        if ($rs->fields[$this->dbSchema->getFieldNameLocalPassword()] !== null) {
                            if ($rs->fields[$this->dbSchema->getFieldNameLocalPassword()] !== $new_password_hash) {
                                $rs->fields[$this->dbSchema->getFieldNameLocalPassword()]           = $new_password_hash;
                                $rs->fields[$this->dbSchema->getFieldNameLocalPasswordLastChange()] = date('Ymd', time());
                                $rs->update();
                            } else {
                                $auth->action = Action::NEW_PASSWORD_USED_IN_PAST;
                            }
                        } else {
                            //TODO: no idea what to do
                        }
                    }
                    $rs->close();
                    $cn->disconnect();
                default:
                    // TODO implement other database drivers
            }
        }
        return $auth;
    }

    /**
     * @throws Exception
     */
    public function change_password(Struct\Result $auth): Struct\Result
    {
        trigger_error('change_password() is deprecated. Please use changePassword() instead', E_USER_DEPRECATED);
        return $this->changePassword($auth);
    }

    public function getUserID(string $username): ?int
    {
        return null;
    }

    final public static function passwordHash(string $password, string|int|null $algorithm = null, ?int $cost = null, ?string $salt = null): string
    {
        // TODO: better yet, generate a pwd and send it by mail, start pwd expires after 24h
        // TODO: pwd reset mail, save reset pwd separate (in case of unintended reset)
        if ($algorithm === null) {
            $algorithm = self::$algorithm;
        }
        if ($cost === null) {
            $cost = self::$cost;
        }
        $options['cost'] = $cost;
        if ($salt !== null) {
            $options['salt'] = $salt;
        }
        return password_hash($password, $algorithm, $options);
    }

    final public static function password_hash(string $password, string|int|null $algorithm = null, ?int $cost = null, ?string $salt = null): string
    {
        trigger_error('password_hash() is deprecated. Please use passwordHash() instead', E_USER_DEPRECATED);
        return self::passwordHash($password, $algorithm, $cost, $salt);
    }

    private function passwordNeedsRehash(string $hash, Struct\Result $auth): bool
    {
        $options['cost'] = $auth->password_cost;
        if ($auth->password_salt !== null) {
            $options['salt'] = $auth->password_salt;
            Debug::notice('Salt for password defined, this might not be your best idea, better leave Salt null (read php password_hash manual)');
        }
        return password_needs_rehash($hash, $auth->password_algorithm, $options);
    }

    private function passwordPolicyCheck(string $password): bool
    {
        $policy                             = true;
        $min_password_length                = 8;
        $min_number_of_numbers              = 1;
        $min_number_of_characters           = 1;
        $min_number_of_lowercase_characters = 0;
        $min_number_of_uppercase_characters = 0;
        $min_number_of_special_characters   = 1;
        if (strlen($password) < $min_password_length) {
            return false;
        }
        if ($min_number_of_numbers > 0 && preg_match_all("/[0-9]/", $password, $x) === 0) {
            return false;
        }
        if ($min_number_of_characters > 0 && preg_match_all("/[a-zA-Z]/", $password, $x) === 0) {
            return false;
        }
        if ($min_number_of_lowercase_characters > 0 && preg_match_all("/[a-z]/", $password, $x) === 0) {
            return false;
        }
        if ($min_number_of_uppercase_characters > 0 && preg_match_all("/[A-Z]/", $password, $x) === 0) {
            return false;
        }
        if ($min_number_of_special_characters > 0 && preg_match_all("/[\W]/", $password, $x) === 0) {
            return false;
        }
        return $policy;
    }

    /**
     * @throws Exception
     */
    private function passwordUpdate(Struct\Result $auth, string $hash): void
    {
        $passColumn = $this->dbSchema->getFieldNameLocalPassword();
        $table      = $this->dbSchema->getTableName();
        $userColumn = $this->dbSchema->getFieldNameUserId();
        $tmp        = Database::getSingle('SELECT '.$passColumn.' FROM '.$table.' WHERE '.$userColumn.'=:userId', ['userId' => $auth->getUserId()]);
        if ($tmp !== null) {
            Database::update('UPDATE '.$table.' SET '.$passColumn.'=:hash WHERE '.$userColumn.'=:userId', ['userId' => $auth->getUserId(), 'hash' => $hash]);
        }
    }

    /**
     * @throws Exception
     */
    private function passwordVerify(Struct\Result $auth, string $password, string $hash): bool
    {
        $verify = password_verify($password, $hash);
        if ($verify === true && $this->passwordNeedsRehash($hash, $auth)) {
            Debug::debug(__METHOD__.": password needs rehash");
            $this->passwordUpdate($auth, self::password_hash($password, $auth->password_algorithm, $auth->password_cost));
        }
        return $verify;
    }
}
