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
                    $result->setError(AuthenticationError::INVALID_CREDENTIALS);
                    break;
                case Action::CHANGE_PASSWORD:
                    $result->setError(AuthenticationError::CHANGE_PASSWORD);
                    break;
                case Action::DISPLAY_TOO_MANY_FAILED_ATTEMPS:
                    $result->setError(AuthenticationError::DISPLAY_TOO_MANY_FAILED_ATTEMPTS);
                    break;
                case Action::NEW_PASSWORD_REPEAT_FAILED:
                    $result->setError(AuthenticationError::NEW_PASSWORD_REPEAT_FAILED);
                    break;
                case Action::NEW_PASSWORD_USED_IN_PAST:
                    $result->setError(AuthenticationError::NEW_PASSWORD_USED_IN_PAST);
                    break;
                case Action::NEW_PASSWORD_DOESNT_MATCH_POLICY:
                    $result->setError(AuthenticationError::NEW_PASSWORD_DOESNT_MATCH_POLICY);
                    break;
                case Action::PASSWORD_EXPIRED:
                    $result->setError(AuthenticationError::PASSWORD_EXPIRED);
                    break;
                case Action::AUTHENTICATION_TARGET_UNREACHABLE:
                    $result->setError(AuthenticationError::AUTHENTICATION_TARGET_UNREACHABLE);
                    break;
                default:
                    $result->setError(AuthenticationError::UNEXPECTED_ERROR);
                    break;
            }
        }
        return $result;
    }
}