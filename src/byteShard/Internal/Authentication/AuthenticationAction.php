<?php

namespace byteShard\Internal\Authentication;

enum AuthenticationAction: string
{
    case PASSWORD_EXPIRED                  = 'credentials_expired';
    case AUTHENTICATION_TARGET_UNREACHABLE = 'auth_provider_unreachable';
    case INVALID_CREDENTIALS               = 'credentials';
    case UNEXPECTED_ERROR                  = 'unexpected';
    case CHANGE_PASSWORD                   = 'change';
    case NO_LOCAL_USER                     = 'no_user';
    case DISPLAY_TOO_MANY_FAILED_ATTEMPTS  = 'too_many_attempts';
    case NEW_PASSWORD_REPEAT_FAILED        = 'repeat_failed';
    case NEW_PASSWORD_USED_IN_PAST         = 'new_pass_used';
    case NEW_PASSWORD_DOESNT_MATCH_POLICY  = 'new_pass_policy';
    case LOGOUT                            = 'logout';
    case SESSION_EXPIRED                   = 'session_expired';

    private const ACTION_KEY = 'action';

    public function processAction(?ProviderInterface $provider): never
    {
        Authentication::logout($provider, $this);
    }

    public function getParameter(): array
    {
        return [self::ACTION_KEY => $this->value];
    }

    public static function getAction(): ?AuthenticationAction
    {
        if (array_key_exists(self::ACTION_KEY, $_GET)) {
            return AuthenticationAction::tryFrom($_GET[self::ACTION_KEY]);
        }
        return null;
    }
}
