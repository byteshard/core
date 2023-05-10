<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal;

use byteShard\DataModelInterface;
use byteShard\Enum;
use byteShard\Exception;
use byteShard\Authentication;
use byteShard\Internal\Schema;
use byteShard\Internal\Authentication\Struct;
use byteShard\Authentication\Enum\Action;
use byteShard\Internal\Authentication\AuthenticationInterface;
use byteShard\Locale;

/**
 * Class Login
 * @package byteShard\Internal
 */
class Login
{
    protected Schema\DB\UserTable        $dbSchema;
    protected string                     $ldapHost;
    protected bool                       $serviceMode = false;
    protected ?AuthenticationInterface   $authenticationObject;
    protected Authentication\Enum\Target $authenticationTarget;
    protected Enum\AccessControlTarget   $accessControlTarget;
    protected Struct\Result              $authenticationResult;
    private DataModelInterface           $dataModel;

    public function __construct(DataModelInterface $dataModel, Enum\AccessControlTarget $accessControlTarget, Authentication\Enum\Target $authTarget, Schema\DB\UserTable $dbSchema, AuthenticationInterface $authObject = null)
    {
        $this->dataModel            = $dataModel;
        $this->accessControlTarget  = $accessControlTarget;
        $this->authenticationTarget = $authTarget;
        $this->authenticationObject = $authObject;
        $this->dbSchema             = $dbSchema;

        $this->authenticationResult = new Struct\Result();
        $this->authenticationResult->setSuccess(false);
    }

    public function setLdapHost(string $host): void
    {
        $this->ldapHost = $host;
    }

    /**
     * @param CredentialsInterface $credentials
     * @return Struct\Result
     */
    public function login(CredentialsInterface $credentials): Struct\Result
    {
        $this->authenticationResult->username = $credentials->getUsername();
        $this->authenticationResult->password = $credentials->getPassword();
        $this->authenticationResult->domain   = $credentials->getDomain();

        if ($this->authenticationObject->checkUsernamePattern($credentials->getUsername()) === false) {
            $this->authenticationResult->failed_text = Locale::get('byteShard.login.checkUsernamePattern.failed');
            return $this->authenticationResult;
        }
        //this can be done with fewer queries and methods but since this only occurs during login, and it greatly helps to understand what's happening here, it is done in a step by step way

        // TODO: implement locale
        // get User ID for the $username, if no ID found, return authenticationResult with ->success=false
        if ($this->getUserID($this->dbSchema, $credentials->getUsername()) === false) {
            $this->authenticationResult->success     = false;
            $this->authenticationResult->failed_text = Locale::get('byteShard.login.userId.notFound');
            $this->authenticationResult->action      = Action::INVALID_CREDENTIALS;
            return $this->authenticationResult;
        }

        // if service mode is active (configured in applicationEnvironment) check if the service account flag is set for this user, if not, return authenticationResult with ->success=false
        // by running this query only if service mode is active this will result in all users being logged out once service mode is activated, even if they have a service account.
        if ($this->serviceMode === true && $this->checkServiceAccount($this->dbSchema, $this->authenticationResult->user_ID) === false) {
            $this->authenticationResult->success     = false;
            $this->authenticationResult->failed_text = Locale::get('byteShard.login.serviceMode.accessDenied');
            return $this->authenticationResult;
        }

        // if access control target is defined on DB, get the target for this user
        if ($this->accessControlTarget === Enum\AccessControlTarget::ACCESS_CONTROL_DEFINED_ON_DB && $this->getAccessControlTarget($this->dbSchema, $this->authenticationResult->user_ID) === false) {
            //if everything works as intended getAccessControlTarget overwrites $this->accessControlTarget with the user stored target which will be used in the next switch
            return $this->authenticationResult;
        }

        switch ($this->accessControlTarget) {
            case Enum\AccessControlTarget::ACCESS_CONTROLLED_BY_DB:
                if ($this->checkAccessControlOnDB($this->dbSchema, $this->authenticationResult->user_ID) === false) {
                    // GrantLogin is false
                    return $this->authenticationResult;
                }
                break;
            case Enum\AccessControlTarget::ACCESS_CONTROLLED_BY_LDAP:
                // access control is evaluated during ldap authentication
                break;
        }

        if ($this->authenticationTarget === Authentication\Enum\Target::AUTH_TARGET_DEFINED_ON_DB && $this->getAuthenticationTarget($this->dbSchema, $this->authenticationResult->user_ID) === false) {
            //if everything works as intended getAuthenticationTarget overwrites $this->authenticationTarget with the user stored target which will be used in the next switch
            return $this->authenticationResult;
        }
        switch ($this->authenticationTarget) {
            case Authentication\Enum\Target::AUTH_TARGET_DB:
                $this->authenticationResult->authTarget = Authentication\Enum\Target::AUTH_TARGET_DB;
                if (!$this->authenticationObject instanceof Authentication\DB) {
                    $this->authenticationObject = new Authentication\DB($this->dbSchema);
                }
                $this->authenticationObject->authenticate($this->authenticationResult);
                break;
            case Authentication\Enum\Target::AUTH_TARGET_LDAP:
                $this->authenticationResult->authTarget = Authentication\Enum\Target::AUTH_TARGET_LDAP;
                if (!$this->authenticationObject instanceof Authentication\Ldap) {
                    $this->authenticationObject = new Authentication\Ldap($this->ldapHost ?? null);
                }
                $this->authenticationObject->authenticate($this->authenticationResult);
                break;
            default:
                throw new Exception(__METHOD__.': Switch needs to be refactored to implement new AuthenticationTarget');
        }
        return $this->authenticationResult;
    }

    /**
     * @param CredentialsInterface $credentials
     * @return Struct\Result
     * @throws Exception
     */
    public function change_password(CredentialsInterface $credentials): Struct\Result
    {
        $this->login($credentials);
        if ($this->authenticationResult->success === true) {
            $this->authenticationResult->action = null;
            if ($credentials->getNewPassword() === $credentials->getNewPasswordRepetition()) {
                $this->authenticationResult->newPassword = $credentials->getNewPassword();
                $this->authenticationResult              = $this->authenticationObject->changePassword($this->authenticationResult);
            } else {
                $this->authenticationResult->action = Action::NEW_PASSWORD_REPEAT_FAILED;
            }
        } else {
            $this->authenticationResult->action = Action::OLD_PASSWORD_WRONG;
        }
        return $this->authenticationResult;
    }

    public function setServiceMode($serviceMode): self
    {
        $this->serviceMode = $serviceMode;
        return $this;
    }

    // ######### Private Functions #########

    /**
     * @param Schema\DB\UserTable $dbSchema
     * @param $userID
     * @return bool
     */
    private function checkAccessControlOnDB(Schema\DB\UserTable $dbSchema, $userID): bool
    {
        $result = $this->dataModel->checkGrantLogin($userID, $dbSchema);
        if ($result === true) {
            Debug::debug(__METHOD__.': User ('.$userID.') is allowed to access the application');
        }
        return $result;
    }

    private function checkServiceAccount(Schema\DB\UserTable $dbSchema, $userID): bool
    {
        $result                                     = $this->dataModel->checkServiceAccount($userID, $dbSchema);
        $this->authenticationResult->serviceAccount = $result;
        if ($result === true) {
            Debug::debug(__METHOD__.': User ('.$userID.') has a serviceAccount');
        } else {
            Debug::debug(__METHOD__.': User ('.$userID.") doesn't have a serviceAccount");
        }
        return $result;
    }

    private function getAccessControlTarget(Schema\DB\UserTable $dbSchema, int|string $userID): bool
    {
        $accessControlTarget = $this->dataModel->getAccessControlTarget($userID, $dbSchema);
        if ($accessControlTarget !== null) {
            $this->accessControlTarget = $accessControlTarget;
            Debug::debug(__METHOD__.': Access control target is defined as : '.$this->accessControlTarget->value);
            return true;
        }
        return false;
    }

    private function getAuthenticationTarget(Schema\DB\UserTable $dbSchema, int|string $userID): bool
    {
        $authenticationTarget = $this->dataModel->getAuthenticationTarget($userID, $dbSchema);
        if ($authenticationTarget !== null) {
            $this->authenticationTarget = $authenticationTarget;
            return true;
        }
        return false;
    }

    /**
     * @param Schema\DB\UserTable $dbSchema
     * @param string $username
     * @return bool
     */
    private function getUserID(Schema\DB\UserTable $dbSchema, string $username): bool
    {
        // an application specific function to get the user id can be supplied this way
        if ($this->authenticationObject !== null) {
            $user_id = $this->authenticationObject->getUserID($username);
            if ($user_id !== null) {
                Debug::debug(__METHOD__.': User_ID ('.$user_id.') supplied by Authentication object for User ('.$username.')');
                $this->authenticationResult->user_ID = $user_id;
                return true;
            }
        }

        $this->authenticationResult->user_ID = $this->dataModel->getUserId($username, $dbSchema);
        if ($this->authenticationResult->user_ID !== null) {
            Debug::debug(__METHOD__.': Found User_ID ('.$this->authenticationResult->user_ID.') for User ('.$username.')');
            return true;
        }
        return false;
    }
}
