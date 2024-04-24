<?php

namespace byteShard\Internal\Authentication;

use byteShard\Internal\Login\Struct\Credentials;

interface LocalProviderInterface
{
    public function authenticate(Credentials $credentials): AuthenticationResult;
}
