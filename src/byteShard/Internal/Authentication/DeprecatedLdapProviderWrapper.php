<?php

namespace byteShard\Internal\Authentication;

use byteShard\Authentication\Enum\Action;
use byteShard\Internal\Authentication\Struct\Result;
use byteShard\Internal\Login\Struct\Credentials;

class DeprecatedLdapProviderWrapper implements LdapProviderInterface
{

    public function __construct(private readonly AuthenticationInterface $deprecatedProvider)
    {
    }

    public function authenticate(Credentials $credentials): AuthenticationResult
    {
        $authenticationResult = new Result();
        $authenticationResult->setSuccess(false);
        $authenticationResult->username = $credentials->getUsername();
        $authenticationResult->password = $credentials->getPassword();
        $authenticationResult->domain   = $credentials->getDomain();

        $this->deprecatedProvider->authenticate($authenticationResult);

        $result = new AuthenticationResult($authenticationResult->success);
        if ($authenticationResult->action !== null) {
            switch ($authenticationResult->action) {
                case Action::OLD_PASSWORD_WRONG:
                case Action::INVALID_CREDENTIALS:
                    $result->setAction(AuthenticationAction::INVALID_CREDENTIALS);
                    break;
                case Action::CHANGE_PASSWORD:
                    $result->setAction(AuthenticationAction::CHANGE_PASSWORD);
                    break;
                case Action::DISPLAY_TOO_MANY_FAILED_ATTEMPS:
                    $result->setAction(AuthenticationAction::DISPLAY_TOO_MANY_FAILED_ATTEMPTS);
                    break;
                case Action::NEW_PASSWORD_REPEAT_FAILED:
                    $result->setAction(AuthenticationAction::NEW_PASSWORD_REPEAT_FAILED);
                    break;
                case Action::NEW_PASSWORD_USED_IN_PAST:
                    $result->setAction(AuthenticationAction::NEW_PASSWORD_USED_IN_PAST);
                    break;
                case Action::NEW_PASSWORD_DOESNT_MATCH_POLICY:
                    $result->setAction(AuthenticationAction::NEW_PASSWORD_DOESNT_MATCH_POLICY);
                    break;
                case Action::PASSWORD_EXPIRED:
                    $result->setAction(AuthenticationAction::PASSWORD_EXPIRED);
                    break;
                case Action::AUTHENTICATION_TARGET_UNREACHABLE:
                    $result->setAction(AuthenticationAction::AUTHENTICATION_TARGET_UNREACHABLE);
                    break;
                default:
                    $result->setAction(AuthenticationAction::UNEXPECTED_ERROR);
                    break;
            }
        }
        return $result;
    }
}