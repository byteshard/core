<?php

namespace byteShard\Internal\Authentication;

interface CustomIdentityProvider
{
    public function getCustomIdentityProvider(): ?ProviderInterface;
}