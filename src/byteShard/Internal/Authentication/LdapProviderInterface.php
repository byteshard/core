<?php

namespace byteShard\Internal\Authentication;

use byteShard\Internal\Login\Struct\Credentials;

interface LdapProviderInterface
{
    public function authenticate(Credentials $credentials): AuthenticationResult;
}