<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard;

use byteShard\Internal\Sanitizer;
use byteShard\Internal\Schema;

/**
 * Class LoginForm
 * @package byteShard
 */
abstract class LoginForm
{
    protected string           $errorMessage;
    protected string           $version;
    protected string           $appName;
    protected string           $favicon;
    protected string           $input_domain;
    protected string           $input_email;
    protected string           $input_password;
    protected string           $input_password_new;
    protected string           $input_password_repeat;
    protected string           $input_username;
    protected string           $button_login;
    protected string           $button_password_change;
    protected string           $button_password_forgot;
    protected string           $button_password_request;
    protected bool             $useLocalLogin  = false;
    protected bool             $useLdapLogin   = false;
    protected bool             $changePassword = false;
    protected bool             $oldPassInvalid = false;
    protected bool             $newPassInvalid = false;
    protected bool             $newPassNoMatch = false;
    protected bool             $newPassUsed    = false;
    protected bool             $serviceMode    = false;
    protected bool             $loginFailed    = false;
    protected bool             $pass_expired   = false;
    protected bool             $error          = false;
    protected bool             $logFileAccess  = true;
    protected bool             $loggedOut      = false;
    protected bool             $sessionTimeout = false;
    protected bool             $showForgotPass = false;
    protected ?int             $waitSeconds    = null;
    protected Schema\LoginForm $loginFormSchema;

    public function __construct(Schema\LoginForm $loginFormSchema, bool $forgotPass, string $appName, string $version, bool $serviceMode, string $favicon)
    {
        $this->loginFormSchema = $loginFormSchema;
        $this->showForgotPass  = $forgotPass;
        $this->appName         = $appName;
        $this->version         = $version;
        $this->serviceMode     = $serviceMode;
        $this->favicon         = $favicon;
        if (isset($_POST[$this->loginFormSchema->input_username])) {
            $_POST[$this->loginFormSchema->input_username] = Sanitizer::sanitize($_POST[$this->loginFormSchema->input_username]);
        } else {
            $_POST[$this->loginFormSchema->input_username] = '';
        }
    }

    abstract public function printForm(): void;

    public function setLoginFailed(bool $bool): void
    {
        $this->loginFailed = $bool;
    }

    public function setError(bool $bool = true): void
    {
        $this->error = $bool;
    }

    public function setLogFileAccess(bool $bool = false): void
    {
        $this->logFileAccess = $bool;
    }

    public function setLoggedOut(bool $bool = true): void
    {
        $this->loggedOut = $bool;
    }

    public function setSecondsToWait(?int $seconds): void
    {
        $this->waitSeconds = $seconds;
    }

    public function setSessionTimeout(bool $bool): void
    {
        $this->sessionTimeout = $bool;
    }

    public function setErrorMessage(string $string): void
    {
        $this->errorMessage = $string;
    }

    public function setPasswordChange(bool $bool): void
    {
        $this->changePassword = $bool;
    }

    public function setOldPasswordInvalid(bool $bool = true): void
    {
        $this->oldPassInvalid = $bool;
    }

    public function setNewPasswordInvalid(bool $bool = true): void
    {
        $this->newPassInvalid = $bool;
    }

    public function setNewPasswordsDontMatch(bool $bool = true): void
    {
        $this->newPassNoMatch = $bool;
    }

    public function setNewPasswordAlreadyUsed(bool $bool = true): void
    {
        $this->newPassUsed = $bool;
    }

    public function setPasswordExpired(bool $bool = true): self
    {
        $this->pass_expired = $bool;
        return $this;
    }
}
