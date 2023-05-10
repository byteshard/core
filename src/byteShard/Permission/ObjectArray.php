<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Permission;

use byteShard\Permission;

/**
 * Class ObjectArray
 * @package byteShard\Permission
 */
class ObjectArray extends Permission
{
    public function __construct(array $array, string $accessRightID = 'AccessRight_ID', string $accessType = 'AccessType') {
        foreach ($array as $val) {
            if (isset($val->{$accessRightID}, $val->{$accessType})) {
                $this->permissions[$val->{$accessRightID}] = (int)$val->{$accessType};
            }
        }
    }
}
