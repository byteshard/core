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

    public function isServiceAccount(int $userId, UserTable $schema = null): bool;

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

    public function successfulLoginCallback(int $userId, UserTable $schema = null): bool;

    public function getLastTab(int $userId, UserTable $schema): string;

    public function setLastTab(int $userId, string $lastTab, UserTable $schema): bool;

    public function checkGrantLogin(int|string $userId, UserTable $schema): bool;

    public function checkServiceAccount(int|string $userId, UserTable $schema): bool;

    public function getAccessControlTarget(int|string $userId, UserTable $schema): ?AccessControlTarget;

    public function getAuthenticationTarget(int|string $userId, UserTable $schema): ?Target;

    public function getUserId(string $username, UserTable $schema): ?int;
}
