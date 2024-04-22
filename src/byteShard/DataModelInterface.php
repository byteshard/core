<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard;

use byteShard\Authentication\Enum\Target;
use byteShard\Enum\AccessControlTarget;
use byteShard\Internal\Schema\DB\UserTable;

interface DataModelInterface
{
    public function storeUserSetting(string $tabName, string $cellName, string $type, string $item, int $userId, $value): bool;

    public function deleteUserSetting(string $tabName, string $cellName, string $type, string $item, int $userId): bool;

    public function isServiceAccount(int $userId): bool;

    /**
     * @param int $userId
     * @return array
     * #[ArrayShape([
     * $result[0]->tab => 'string',
     * $result[0]->cell => 'string',
     * $result[0]->type => 'string',
     * $result[0]->value => 'string'])]
     */
    public function getCellSize(int $userId): array;

    public function successfulLoginCallback(int $userId): bool;

    public function getLastTab(int $userId): string;

    public function setLastTab(int $userId, string $lastTab): bool;

    public function checkGrantLogin(int $userId): bool;

    public function getUserId(string $username): ?int;

    /** return null in case user is not found, empty string if no password is set or password hash */
    public function getPasswordHash(string $username): ?string;

    public function updatePasswordHash(string $username, string $password): void;

    public function getPasswordExpiration(string $username): ?object;
}
