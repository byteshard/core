<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Permission;

use byteShard\Permission;

/**
 * Class PermissionArray
 * @package byteShard\Permission
 */
class PermissionArray extends Permission
{
    /**
     * PermissionArray constructor.
     * @param array $array
     * @param string $accessRightId
     * @param string $accessType
     */
    public function __construct(array $array = [], string $accessRightId = 'AccessRight_ID', string $accessType = 'AccessType') {
        foreach ($array as $val) {
            if (isset($val[$accessRightId], $val[$accessType])) {
                $this->permissions[$val[$accessRightId]] = (int)$val[$accessType];
            }
        }
    }

    /**
     * @API
     * @param array $array
     */
    public function setIDArray(array $array) {
        foreach ($array as $permission => $ids) {
            $this->permissionIdArray[$permission] = $ids;
        }
    }
}
