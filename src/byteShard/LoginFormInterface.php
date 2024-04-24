<?php

namespace byteShard;

use byteShard\Internal\Login\Struct\Credentials;

interface LoginFormInterface
{
    public function setBaseUrl(string $url): void;
    
    public function printLoginForm(): void;

    public function getCredentials(): Credentials;

    public function getLoginButtonName(): string;
}
