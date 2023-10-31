<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Login;

use byteShard\Internal\Server;
use byteShard\Locale;
use byteShard\Internal\Schema\LoginForm;

class Template
{
    public function __construct(
        private readonly LoginForm $schema,
        private readonly array     $javaScripts,
        private readonly array     $css,
        private readonly string    $favicon,
        private readonly string    $appName,
        private readonly string    $version = ''
    ) {}

    public function printLoginForm(): void
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
        array_push($html, ...$this->getLoginContent());
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

    private function getLoginContent(): array
    {
        $content[] = '<form action="'.Server::getBaseUrl().'" method="post" name="LoginForm">';
        $content[] = '<div id="UsernameLabel"><span>'.Locale::get('byteShard.login.user').'</span></div>';
        $content[] = '<div id="LoginUsername"><input class="name" type="text" name="'.$this->schema->input_username.'" size="15" value=""></div>';
        $content[] = '<div id="PasswordLabel"><span>'.Locale::get('byteShard.login.password').'</span></div>';
        $content[] = '<div id="LoginPassword"><input class="pass" type="password" name="'.$this->schema->input_password.'" size="15" autocomplete="off"></div>';
        $content[] = '<div id="DomainLabel"><span>domain:</span></div>';
        $content[] = '<div id="LoginButton"><input class="button" type="submit" value="'.Locale::get('byteShard.login.login').'" name="'.$this->schema->button_login.'"></div>';
        $content[] = '</form>';
        $content[] = '<form action="'.Server::getBaseUrl().'" method="post" name="forgotPassForm">';
        $content[] = '<div id="forgotPassButton"><input class="button" type="submit" value="'.Locale::get('byteShard.login.forgot').'" name="'.$this->schema->button_password_forgot.'"></div>';
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
}
