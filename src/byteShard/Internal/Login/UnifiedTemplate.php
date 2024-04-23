<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Login;

use byteShard\Internal\Authentication\Providers;
use byteShard\Internal\Login\Struct\Credentials;
use byteShard\Internal\Server;
use byteShard\Locale;
use byteShard\LoginFormInterface;

class UnifiedTemplate implements LoginFormInterface
{
    private const BUTTON_LOGIN              = 'login';
    private const BUTTON_CHANGE_PASSWORD    = 'change';
    private const BUTTON_FORGOT             = 'forgot';
    private const INPUT_USERNAME            = 'username';
    private const INPUT_PASSWORD            = 'password';
    private const INPUT_DOMAIN              = 'domain';
    private const INPUT_NEW_PASSWORD        = 'new_password';
    private const INPUT_NEW_PASSWORD_REPEAT = 'new_password_repeat';

    private string $baseUrl;

    public function __construct(
        private readonly string $favicon,
        private readonly string $appName,
        private readonly array  $javaScripts = [],
        private readonly array  $css = [],
        private readonly string $version = ''
    )
    {
        $this->baseUrl = Server::getBaseUrl();
    }

    public function setBaseUrl(string $url): void
    {
        $this->baseUrl = $url;
    }

    public function printLoginForm(string $target = ''): void
    {
        $html[] = '<!DOCTYPE html>';
        $html[] = '<html>';
        array_push($html, ...$this->getHead());
        $html[] = '<body>';
        $html[] = '<div id="ContentFrame">';
        $html[] = '<div id="LoginContainer">';
        $html[] = '<div id="LoginFrame" class="loginPage">';
        $html[] = '<div id="LoginTop"></div>';
        $html[] = '<div id="LoginContent">';
        array_push($html, ...$this->getLoginContent($target));
        $html[] = '</div>';
        array_push($html, ...$this->getServiceModeContent());
        array_push($html, ...$this->getLoginFailedContent());
        array_push($html, ...$this->getSessionTimeoutContent());
        array_push($html, ...$this->getErrorContent());
        array_push($html, ...$this->getLoggedOutContent());
        if ($this->version !== '') {
            $html[] = '<div id="LoginBottom"><span>v'.$this->version.'</span></div>';
        }
        $html[] = '<div class="chglogButtonNext_deakt"></div>';
        $html[] = '</div>';
        array_push($html, ...$this->getChangelog());
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</body>';
        $html[] = '</html>';
        print implode('', $html);
    }

    private function getHead(): array
    {
        $head[] = '<head>';
        $head[] = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $head[] = '<title>'.$this->appName.'</title>';
        array_push($head, ...$this->javaScripts);
        array_push($head, ...$this->css);
        $head[] = '<link rel="SHORTCUT ICON" href="'.$this->favicon.'">';
        $head[] = '</head>';
        return $head;
    }

    private function getLoginContent(string $target): array
    {

        $content[] = '<form action="'.$this->baseUrl.'" method="post" name="LoginForm">';
        $content[] = '<div id="UsernameLabel"><span>'.Locale::get('byteShard.login.user').'</span></div>';
        $content[] = '<div id="LoginUsername"><input class="name" type="text" name="'.self::INPUT_USERNAME.'" size="15" value=""></div>';
        $content[] = '<div id="PasswordLabel"><span>'.Locale::get('byteShard.login.password').'</span></div>';
        $content[] = '<div id="LoginPassword"><input class="pass" type="password" name="'.self::INPUT_PASSWORD.'" size="15" autocomplete="off"></div>';
        $content[] = '<div id="DomainLabel"><span>domain:</span></div>';
        $content[] = '<div id="LoginButton"><button class="button" type="submit" value="'.Providers::LOCAL->value.'" name="'.self::BUTTON_LOGIN.'">'.Locale::get('byteShard.login.login').'</button></div>';
        $content[] = '<div id="LoginButton2"><button class="button" type="submit" value="'.Providers::LDAP->value.'" name="'.self::BUTTON_LOGIN.'">LDAP</button></div>';
        $content[] = '</form>';
        $content[] = '<form action="'.$this->baseUrl.'" method="post" name="forgotPassForm">';
        $content[] = '<div id="forgotPassButton"><button class="button" type="submit" value="'.self::BUTTON_FORGOT.'" name="'.self::BUTTON_FORGOT.'">'.Locale::get('byteShard.login.forgot').'</div>';
        $content[] = '</form>';
        $content[] = '<form action="'.$this->baseUrl.'" method="post" name="LoginForm">';
        $content[] = '<div id="LoginOauthButton"><button class="button" type="submit" value="'.Providers::OAUTH->value.'" name="'.self::BUTTON_LOGIN.'">OAuth</button></div>';
        $content[] = '</form>';
        return $content;
    }

    private function getServiceModeContent(): array
    {
        return [];
    }

    private function getLoginFailedContent(): array
    {
        return [];
    }

    private function getSessionTimeoutContent(): array
    {
        return [];
    }

    private function getErrorContent(): array
    {
        if (array_key_exists('error', $_GET)) {
            switch ($_GET['error']) {
                case 'nolog':
                    return ['Foo'];
            }
        }
        return [];
    }

    private function getLoggedOutContent(): array
    {
        return [];
    }

    private function getChangelog(): array
    {
        return [];
    }

    public function getCredentials(): Credentials
    {
        $credentials = new Credentials();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginUser = (strlen($_POST[self::INPUT_USERNAME]) > 0) ? $_POST[self::INPUT_USERNAME] : null;
            $credentials->setUsername(array_key_exists(self::INPUT_USERNAME, $_POST) && strlen($_POST[self::INPUT_USERNAME]) > 0 ? $_POST[self::INPUT_USERNAME] : null);
            $credentials->setPassword(array_key_exists(self::INPUT_PASSWORD, $_POST) && strlen($_POST[self::INPUT_PASSWORD]) > 0 ? mb_convert_encoding($_POST[self::INPUT_PASSWORD], 'ISO-8859-1', 'UTF-8') : null);
            $credentials->setDomain(array_key_exists(self::INPUT_DOMAIN, $_POST) && strlen($_POST[self::INPUT_DOMAIN]) > 0 ? mb_convert_encoding($_POST[self::INPUT_DOMAIN], 'ISO-8859-1', 'UTF-8') : null);
            $credentials->setNewPassword(array_key_exists(self::INPUT_NEW_PASSWORD, $_POST) && strlen($_POST[self::INPUT_NEW_PASSWORD]) > 0 ? mb_convert_encoding($_POST[self::INPUT_NEW_PASSWORD], 'ISO-8859-1', 'UTF-8') : null);
            $credentials->setNewPasswordRepeat(array_key_exists(self::INPUT_NEW_PASSWORD_REPEAT, $_POST) && strlen($_POST[self::INPUT_NEW_PASSWORD_REPEAT]) > 0 ? mb_convert_encoding($_POST[self::INPUT_NEW_PASSWORD_REPEAT], 'ISO-8859-1', 'UTF-8') : null);
            if (array_key_exists(self::BUTTON_LOGIN, $_POST)) {
                $credentials->setAction('login');
            } elseif (array_key_exists(self::BUTTON_CHANGE_PASSWORD, $_POST)) {
                $credentials->setAction('changePass');
            } elseif (array_key_exists(self::BUTTON_FORGOT, $_POST)) {
                $credentials->setAction('forgotPass');
            }
            if ($credentials->getAction() !== '' && (empty($credentials->getUsername()) || empty($credentials->getPassword()))) {
                $credentials->setAction('');
            }
        }
        /*if ($this->useLowerCaseUserName === true && $this->loginUser !== null) {
            $this->loginUser = strtolower($this->loginUser);
        }*/
        return $credentials;
    }

    public function getLoginButtonName(): string
    {
        return self::BUTTON_LOGIN;
    }


}
