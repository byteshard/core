<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Authentication;

use byteShard\DataModelInterface;
use byteShard\Exception;
use byteShard\Internal\Authentication\AuthenticationError;
use byteShard\Internal\Authentication\AuthenticationResult;
use byteShard\Internal\Debug;
use DateTime;
use byteShard\Authentication\Enum\Action;

/**
 * Class DB
 * @exceptionId 00003
 * @package byteShard\Internal\Authentication
 */
class DB implements AuthenticationInterface
{
    private static string $defaultAlgorithm = PASSWORD_DEFAULT;
    private static int    $defaultCost      = 12;

    /**
     * DB constructor.
     */
    public function __construct(private readonly DataModelInterface $dataModel)
    {
    }

    /**
     * @throws Exception|\Exception
     */
    public function authenticate(string $username, string $password, string|int|null $algorithm = null, ?int $cost = null, ?string $salt = null): AuthenticationResult
    {
        $result = new AuthenticationResult();
        if ($algorithm === null) {
            $algorithm = self::$defaultAlgorithm;
        }
        if ($cost <= 0 || $cost === null) {
            $cost = self::$defaultCost;
        }
        $passwordHash = $this->dataModel->getPasswordHash($username);
        if ($passwordHash === null) {
            Debug::info(__METHOD__.': User '.$username.' not found');
            return $result;
        }
        if (empty($passwordHash)) {
            Debug::info(__METHOD__.': Password column empty for user: '.$username);
            return $result;
        }
        if ($this->passwordVerify($username, $password, $passwordHash, $cost, $algorithm, $salt) === true) {
            Debug::debug(__METHOD__.': authentication successful (password ok)');
            $result->setSuccess(true);

            $expiration = $this->dataModel->getPasswordExpiration($username);
            if ($expiration !== null && $expiration->expires && is_numeric($expiration->expiresAfterDays)) {
                $date = new DateTime(date('Ymd'));
                $date->modify('-'.$expiration->expiresAfterDays.'days');
                if ($date->format('Ymd') > $expiration->lastChange) {
                    $result->setError(AuthenticationError::CHANGE_PASSWORD);
                }
            }
            return $result;
        } else {
            $result->setError(AuthenticationError::INVALID_CREDENTIALS);
        }
        return $result;
    }

    public function changePassword(string $username, string $newPassword, string|int|null $algorithm = null, ?int $cost = null, ?string $salt = null): ?int
    {
        if ($this->passwordPolicyCheck($newPassword) === false) {
            return Action::NEW_PASSWORD_DOESNT_MATCH_POLICY;
        } else {
            if ($algorithm === null) {
                $algorithm = self::$defaultAlgorithm;
            }
            $newPasswordHash = self::passwordHash($newPassword, $algorithm, $cost, $salt);
            $oldPasswordHash = $this->dataModel->getPasswordHash($username);
            if ($oldPasswordHash === $newPasswordHash) {
                return Action::NEW_PASSWORD_USED_IN_PAST;
            }
            $this->dataModel->updatePasswordHash($username, $newPasswordHash);
        }
        return null;
    }

    final public static function passwordHash(string $password, string|int|null $algorithm = null, ?int $cost = null, ?string $salt = null): string
    {
        // TODO: better yet, generate a pwd and send it by mail, start pwd expires after 24h
        // TODO: pwd reset mail, save reset pwd separate (in case of unintended reset)
        if ($algorithm === null) {
            $algorithm = self::$defaultAlgorithm;
        }
        if ($cost === null) {
            $cost = self::$defaultCost;
        }
        $options['cost'] = $cost;
        if ($salt !== null) {
            $options['salt'] = $salt;
        }
        return password_hash($password, $algorithm, $options);
    }

    private function passwordNeedsRehash(string $hash, string|int|null $algorithm, int $cost = 12, ?string $salt = null): bool
    {
        $options['cost'] = $cost;
        if ($salt !== null) {
            $options['salt'] = $salt;
            Debug::notice('Salt for password defined, this might not be your best idea, better leave Salt null (read php password_hash manual)');
        }
        return password_needs_rehash($hash, $algorithm, $options);
    }

    private function passwordPolicyCheck(string $password): bool
    {
        $minPasswordLength              = 8;
        $minNumberOfNumbers             = 1;
        $minNumberOfCharacters          = 1;
        $minNumberOfLowercaseCharacters = 0;
        $minNumberOfUppercaseCharacters = 0;
        $minNumberOfSpecialCharacters   = 1;
        if (strlen($password) < $minPasswordLength) {
            return false;
        }
        if ($minNumberOfNumbers > 0 && preg_match_all("/[0-9]/", $password, $x) === 0) {
            return false;
        }
        if ($minNumberOfCharacters > 0 && preg_match_all("/[a-zA-Z]/", $password, $x) === 0) {
            return false;
        }
        if ($minNumberOfLowercaseCharacters > 0 && preg_match_all("/[a-z]/", $password, $x) === 0) {
            return false;
        }
        if ($minNumberOfUppercaseCharacters > 0 && preg_match_all("/[A-Z]/", $password, $x) === 0) {
            return false;
        }
        if ($minNumberOfSpecialCharacters > 0 && preg_match_all("/[\W]/", $password, $x) === 0) {
            return false;
        }
        return true;
    }

    private function passwordVerify(string $username, string $password, string $hash, int $cost, string|int|null $algorithm, ?string $salt = null): bool
    {
        $verify = password_verify($password, $hash);
        if ($verify === true && $this->passwordNeedsRehash($hash, $algorithm, $cost, $salt)) {
            Debug::debug(__METHOD__.": password needs rehash");
            $this->dataModel->updatePasswordHash($username, self::passwordHash($password, $algorithm, $cost));
        }
        return $verify;
    }
}
