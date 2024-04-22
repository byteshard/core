<?php

namespace byteShard\Internal\Authentication;

use byteShard\Internal\Login\Struct\Credentials;

interface ProviderInterface
{
    public function authenticate(?Credentials $credentials = null): bool;

    public function userHasValidAndNotExpiredSession(int $sessionTimeoutInMinutes): bool;

    public function getUsername(): string;

    public function logout(): void;
}
