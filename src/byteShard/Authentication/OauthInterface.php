<?php

namespace byteShard\Authentication;

use League\OAuth2\Client\Provider\AbstractProvider;

interface OauthInterface
{
    public function getOauthProvider(): AbstractProvider;

    public function getJwksCertPath(): string;
}