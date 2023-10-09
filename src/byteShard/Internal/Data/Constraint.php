<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Data;

use byteShard\Enum;

/**
 * Class Constraint
 * @package byteShard\Internal\Data
 */
class Constraint
{
    public string             $field;
    public mixed              $value;
    public Enum\DB\ColumnType $type;

    public function __construct(string $field, mixed $value = null, Enum\DB\ColumnType $type = Enum\DB\ColumnType::INT)
    {
        $this->field = $field;
        $this->value = $value;
        $this->type  = $type;
    }
}
