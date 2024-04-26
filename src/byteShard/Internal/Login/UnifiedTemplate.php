<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Login;

use byteShard\Internal\Authentication\AuthenticationError;
use byteShard\Internal\Authentication\Providers;
use byteShard\Internal\ByteShard\Css;
use byteShard\Internal\ByteShard\Javascript;
use byteShard\Internal\Login\Struct\Credentials;
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

    public function printLoginForm(string $actionTarget, string $appName, string $faviconPath): void
    {
        $html[] = '<!DOCTYPE html>';
        $html[] = '<html>';
        array_push($html, ...$this->getHead($appName, $faviconPath));
        $html[] = '<body>';
        $html[] = '<div id="ContentFrame">';
        $html[] = '<div id="LoginContainer">';
        $html[] = '<div id="LoginFrame" class="loginPage">';
        $html[] = '<div id="LoginTop"></div>';
        $html[] = '<div id="LoginContent">';
        array_push($html, ...$this->getLoginContent($actionTarget));
        $html[] = '</div>';
        array_push($html, ...$this->getServiceModeContent());
        array_push($html, ...$this->getLoginFailedContent());
        array_push($html, ...$this->getSessionTimeoutContent());
        array_push($html, ...$this->getErrorContent());
        array_push($html, ...$this->getLoggedOutContent());
        $html[] = '<div class="chglogButtonNext_deakt"></div>';
        $html[] = '</div>';
        array_push($html, ...$this->getChangelog());
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</body>';
        $html[] = '</html>';
        print implode('', $html);
    }

    private function getHead(string $appName, string $faviconPath): array
    {
        $head[] = '<head>';
        $head[] = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $head[] = '<title>'.$appName.'</title>';
        $js     = new Javascript('js');
        $css    = new Css('css');
        array_push($head, ...$js->includeJavascripts(['login.js'], '', '/bs'));
        array_push($head, ...$css->includeCss(['login.css'], '', '/bs'));
        $head[] = '<link rel="SHORTCUT ICON" href="'.$faviconPath.'">';
        $head[] = '</head>';
        return $head;
    }

    protected function getAuthenticationError(): ?AuthenticationError
    {
        if (array_key_exists('error', $_GET)) {
            return AuthenticationError::tryFrom($_GET['error']);
        }
        return null;
    }

    private function getLoginContent(string $actionTarget): array
    {
        $content[] = '<form action="'.$actionTarget.'" method="post" name="LoginForm">';
        $content[] = '<div id="UsernameLabel"><span>'.Locale::get('byteShard.login.user').'</span></div>';
        $content[] = '<div id="LoginUsername"><input class="name" type="text" name="'.self::INPUT_USERNAME.'" size="15" value=""></div>';
        $content[] = '<div id="PasswordLabel"><span>'.Locale::get('byteShard.login.password').'</span></div>';
        $content[] = '<div id="LoginPassword"><input class="pass" type="password" name="'.self::INPUT_PASSWORD.'" size="15" autocomplete="off"></div>';
        $content[] = '<div id="DomainLabel"><span>domain:</span></div>';
        $content[] = '<div id="LoginButton"><button class="button" type="submit" value="'.Providers::LOCAL->value.'" name="'.self::BUTTON_LOGIN.'">'.Locale::get('byteShard.login.login').'</button></div>';
        $content[] = '<div id="LoginButton2"><button class="button" type="submit" value="'.Providers::LDAP->value.'" name="'.self::BUTTON_LOGIN.'">LDAP</button></div>';
        $content[] = '</form>';
        $content[] = '<form action="'.$actionTarget.'" method="post" name="forgotPassForm">';
        $content[] = '<div id="forgotPassButton"><button class="button" type="submit" value="'.self::BUTTON_FORGOT.'" name="'.self::BUTTON_FORGOT.'">'.Locale::get('byteShard.login.forgot').'</div>';
        $content[] = '</form>';
        $content[] = '<form action="'.$actionTarget.'" method="post" name="LoginForm">';
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
        $error = $this->getAuthenticationError();
        switch ($error) {
            case AuthenticationError::INVALID_CREDENTIALS:
                return [];
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
        return $credentials;
    }

    public function getSelectedAuthenticationProvider(): ?Providers
    {
        return Providers::tryFrom($_POST[self::BUTTON_LOGIN]);
    }
}
