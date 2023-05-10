<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Permission;

use byteShard\Permission;
use byteShard\Database;

/**
 * Class Query
 * @package byteShard\Permission
 */
class Query extends Permission
{
    public function __construct(string $permissionQuery, string $accessRightID = 'AccessRight_ID', string $accessType = 'AccessType') {
        $records = Database::getArray($permissionQuery);
        foreach ($records as $record) {
            if (isset($record->{$accessRightID}, $record->{$accessType})) {
                $this->permissions[$record->{$accessRightID}] = (int)$record->{$accessType};
            }
        }
        unset($records);
    }
}
