<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Schema;

use stdClass;

/**
 * Class LoginForm
 * @package byteShard\Internal\Schema
 */
class LoginForm extends stdClass
{
    public string $button_login            = 'login';
    public string $button_password_change  = 'change';
    public string $button_password_forgot  = 'forgot';
    public string $button_password_request = 'request';
    public string $input_domain            = 'domain';
    public string $input_email             = 'email';
    public string $input_password          = 'password';
    public string $input_password_new      = 'passwordNew';
    public string $input_password_repeat   = 'passwordRepeat';
    public string $input_username          = 'username';
    public array  $domain_array            = [];
    public bool   $show_domains            = true;
}
