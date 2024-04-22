<?php

namespace byteShard\Internal\Authentication;

enum Providers: string
{
    case OAUTH = 'oauth';
    case LOCAL = 'local';
    case LDAP = 'ldap';
}
