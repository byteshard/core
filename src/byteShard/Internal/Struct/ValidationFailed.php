<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Struct;

use stdClass;

/**
 * Class ValidationFailed
 * @package byteShard\Internal\Struct
 */
class ValidationFailed extends stdClass
{
    public int   $failedValidations          = 0;
    public array $failedValidationsDataArray = [];
}
