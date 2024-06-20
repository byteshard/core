<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard;

use byteShard\Enum\Access;
use UnitEnum;

/**
 * Class Permission
 * @package byteShard
 */
class Permission
{
    protected array $permissions = [];

    protected array $permissionIdArray = [];

    protected function setPermissionAccess(UnitEnum $permission, Access $access): void
    {
        $this->permissions[$permission->name] = $access->value;
    }

    /**
     * @API
     * @param string $permission
     * @return int
     */
    public function getPermissionAccessType(string $permission): int
    {
        return array_key_exists($permission, $this->permissions) ? $this->permissions[$permission] : 0;
    }

    /**
     * @API
     * @param string $permission
     * @return array
     */
    public function getPermissionIDArray(string $permission): array
    {
        return array_key_exists($permission, $this->permissionIdArray) ? $this->permissionIdArray[$permission] : [];
    }
}
