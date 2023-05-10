<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Authentication;

use byteShard\Authentication\Enum\Action;
use byteShard\Authentication\Enum\Target;
use byteShard\DataModelInterface;
use byteShard\Debug;
use byteShard\Enum\AccessControlTarget;
use byteShard\Environment;
use byteShard\Exception;
use byteShard\Internal\Authentication\AuthenticationInterface;
use byteShard\Internal\Authentication\Struct\Result;
use byteShard\Internal\Login;
use byteShard\Internal\Schema\DB\UserTable;
use byteShard\Internal\Server;

class LocalForm implements IdentityProviderInterface
{
    private object                  $loginFormSchema;
    private bool                    $useLowerCaseUserName = false;
    private AccessControlTarget     $accessControlTarget;
    private Target                  $authenticationTarget;
    private UserTable               $userTableSchema;
    private AuthenticationInterface $authentication;
    private bool                    $serviceMode          = false;
    private string                  $ldapHost             = '';
    /** @var Environment only for callback. Remove once the printLoginForm have been refactored */
    private Environment $environment;

    private ?string            $loginUser                = null;
    private ?string            $loginPassword            = null;
    private ?string            $loginDomain              = null;
    private ?string            $loginNewPassword         = null;
    private ?string            $loginNewPasswordRepeated = null;
    private UserData           $userData;
    private DataModelInterface $dataModel;

    public function __construct(DataModelInterface $dataModel)
    {
        $this->dataModel = $dataModel;
        $this->userData  = new UserData();
    }

    public function setLoginFormSchema(object $loginFormSchema): void
    {
        $this->loginFormSchema = $loginFormSchema;
    }

    public function setLowerCaseUserName(bool $useLowerCaseUserName): void
    {
        $this->useLowerCaseUserName = $useLowerCaseUserName;
    }

    public function setEnvironment(Environment $environment): void
    {
        $this->environment = $environment;
    }

    public function setAccessControlTarget(AccessControlTarget $target): void
    {
        $this->accessControlTarget = $target;
    }

    public function setAuthenticationTarget(Target $target): void
    {
        $this->authenticationTarget = $target;
    }

    public function setUserTableSchema(UserTable $userTableSchema): void
    {
        $this->userTableSchema = $userTableSchema;
    }

    public function setApplicationAuthentication(AuthenticationInterface $authentication): void
    {
        $this->authentication = $authentication;
    }

    public function setServiceMode(bool $serviceMode): void
    {
        $this->serviceMode = $serviceMode;
    }

    public function setLdapHost(string $hostName): void
    {
        $this->ldapHost = $hostName;
    }

    private function getLoginObject(): Login
    {
        $login = new Login($this->dataModel, $this->accessControlTarget, $this->authenticationTarget, $this->userTableSchema, $this->authentication);
        $login->setServiceMode($this->serviceMode);
        $login->setLdapHost($this->ldapHost); // check if needed
        return $login;
    }

    private function getCredentialObject(): Login\Struct\Credentials
    {
        $credentials = new Login\Struct\Credentials();
        $credentials->setUsername($this->loginUser);
        $credentials->setPassword($this->loginPassword);
        $credentials->setDomain($this->loginDomain);
        $credentials->setNewPassword($this->loginNewPassword);
        $credentials->setNewPasswordRepeat($this->loginNewPasswordRepeated);
        return $credentials;
    }

    private function processLoginResult(Result $result): bool
    {
        if ($result->success === true && $result->action === null) {
            $this->userData = new UserData($result->user_ID, $result->username, $result->serviceAccount);
            return true;
        }
        switch ($result->action) {
            case Action::CHANGE_PASSWORD:
                $this->environment->printLoginCallback(Action::CHANGE_PASSWORD);
                exit;
            case Action::DISPLAY_TOO_MANY_FAILED_ATTEMPS:
                $this->environment->printLoginCallback(Action::DISPLAY_TOO_MANY_FAILED_ATTEMPS, $result['secondsToWait']);
                exit;
            case Action::NEW_PASSWORD_REPEAT_FAILED:
                $this->environment->printLoginCallback(Action::NEW_PASSWORD_REPEAT_FAILED);
                exit;
            case Action::OLD_PASSWORD_WRONG:
                $this->environment->printLoginCallback(Action::OLD_PASSWORD_WRONG);
                exit;
            case Action::NEW_PASSWORD_USED_IN_PAST:
                $this->environment->printLoginCallback(Action::NEW_PASSWORD_USED_IN_PAST);
                exit;
            case Action::NEW_PASSWORD_DOESNT_MATCH_POLICY:
                $this->environment->printLoginCallback(Action::NEW_PASSWORD_DOESNT_MATCH_POLICY);
                exit;
            case Action::PASSWORD_EXPIRED:
                $this->environment->printLoginCallback(Action::PASSWORD_EXPIRED);
                exit;
            case Action::AUTHENTICATION_TARGET_UNREACHABLE:
                $this->environment->printLoginCallback(Action::AUTHENTICATION_TARGET_UNREACHABLE);
                exit;
            case Action::INVALID_CREDENTIALS:
                $this->environment->printLoginCallback(Action::INVALID_CREDENTIALS);
                exit;
            default:
                $e = new Exception(__METHOD__.': Authentication\Struct\Result has no action specified. '.$result->failed_text, 100004000);
                $e->setLocaleToken('byteShard.environment.authenticate.no_action');
                throw $e;
        }
    }

    public function authenticate(): bool
    {
        $action = $this->evaluatePostData();

        $host = Server::getHost();
        if ($action === '') {
            // no known form button has been used to submit data
            if (array_key_exists('HTTP_REFERER', $_SERVER) && (str_contains($_SERVER['HTTP_REFERER'], $host)) && isset($_SESSION, $_SESSION['ERROR']) && $_SESSION['ERROR'] === true) {
                // this doesn't really make sense. Refactor error handler to catch invalid file access.
                // currently, invalid file access is true when a log was written and the error handler result object is set to RESULT_OBJECT_LOGIN
                if (!$_SESSION['FILE_ACCESS']) {
                    Debug::warning('Invalid file access');
                    Debug::warning('Action:', is_array($action) ? $action : array($action));
                    Debug::warning('Server:', $_SERVER);
                    Debug::warning('Session:', $_SESSION);
                }
                $this->environment->printLoginCallback('error');
                exit;
            }
            if (array_key_exists('ERROR', $_SESSION) && $_SESSION['ERROR'] === true) {
                session_unset();
                session_destroy();
                $this->environment->printLoginCallback('error');
                exit;
            }
        }

        if ($action !== '') {
            switch ($action) {
                case 'forgotPass':
                    $this->forgotPassword();
                    break;
                case 'login':
                    $login  = $this->getLoginObject();
                    $result = $login->login($this->getCredentialObject());
                    if ($result instanceof Result) {
                        return $this->processLoginResult($result);
                    }
                    $this->invalidLoginResult($action);
                    break;
                case 'changePass':
                    $login  = $this->getLoginObject();
                    $result = $login->change_password($this->getCredentialObject());
                    if ($result instanceof Result) {
                        return $this->processLoginResult($result);
                    }
                    $this->invalidLoginResult($action);
                    break;
                default:
                    $this->environment->printLoginCallback('error');
                    exit;
            }
            return true;
        }
        return false;
    }


    private function invalidLoginResult(?string $action)
    {
        Debug::info('Hint: $result not instanceof Authentication\Struct\Result (returned by Login method '.$action.')', array('Action' => $action, 'Session' => $_SESSION));
        $this->environment->printLoginCallback('error');
        exit;
    }

    private function forgotPassword()
    {
        $this->environment->printLoginCallback('passwordReset');
        exit;
    }

    private function evaluatePostData(): string
    {
        $action = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (array_key_exists($this->loginFormSchema->button_password_forgot, $_POST)) {
                $action = 'forgotPass';
            }
            if (array_key_exists($this->loginFormSchema->button_password_change, $_POST)) {
                $action                         = 'changePass';
                $this->loginUser                = (strlen($_POST[$this->loginFormSchema->input_username]) > 0) ? $_POST[$this->loginFormSchema->input_username] : null;
                $this->loginPassword            = (strlen($_POST[$this->loginFormSchema->input_password]) > 0) ? utf8_decode($_POST[$this->loginFormSchema->input_password]) : null;
                $this->loginDomain              = (isset($_POST[$this->loginFormSchema->input_domain]) && strlen($_POST[$this->loginFormSchema->input_domain]) > 0) ? utf8_decode($_POST[$this->loginFormSchema->input_domain]) : null;
                $this->loginNewPassword         = (strlen($_POST[$this->loginFormSchema->input_password_new]) > 0) ? utf8_decode($_POST[$this->loginFormSchema->input_password_new]) : null;
                $this->loginNewPasswordRepeated = (strlen($_POST[$this->loginFormSchema->input_password_repeat]) > 0) ? utf8_decode($_POST[$this->loginFormSchema->input_password_repeat]) : null;
            }
            if (array_key_exists($this->loginFormSchema->button_login, $_POST)) {
                $action              = 'login';
                $this->loginUser     = (strlen($_POST[$this->loginFormSchema->input_username]) > 0) ? $_POST[$this->loginFormSchema->input_username] : null;
                $this->loginPassword = (strlen($_POST[$this->loginFormSchema->input_password]) > 0) ? $_POST[$this->loginFormSchema->input_password] : null;
                $this->loginDomain   = isset($_POST[$this->loginFormSchema->input_domain]) && (strlen($_POST[$this->loginFormSchema->input_domain]) > 0) ? utf8_decode($_POST[$this->loginFormSchema->input_domain]) : null;
            }
        }
        if ($action !== '' && (empty($this->loginUser) || empty($this->loginPassword))) {
            Debug::info('Hint: login_form_data incomplete');
            $action = '';
        }
        if ($this->useLowerCaseUserName === true && $this->loginUser !== null) {
            $this->loginUser = strtolower($this->loginUser);
        }
        return $action;
    }

    public function getUserData(): UserDataInterface
    {
        return $this->userData;
    }

    public function logout()
    {
        $this->environment->printLoginCallback('logout');
    }

}
