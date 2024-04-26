<?php

namespace byteShard;

use byteShard\Internal\Authentication\Providers;
use byteShard\Internal\Login\Struct\Credentials;

interface LoginFormInterface
{
    public function printLoginForm(string $actionTarget, string $appName, string $faviconPath): void;

    public function getCredentials(): Credentials;

    public function getSelectedAuthenticationProvider(): ?Providers;
}
