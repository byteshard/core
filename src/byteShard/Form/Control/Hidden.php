<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;
use byteShard\Internal\Form;
use byteShard\Enum;

/**
 * Class Hidden
 * @package byteShard\Form\Control
 */
class Hidden extends Form\FormObject
{
    protected string $type        = 'hidden';
    protected ?string $dbColumnType = Enum\DB\ColumnType::VARCHAR;
    use Form\Name;
    use Form\Userdata;
    use Form\Value;

    public function __construct($name, $value) {
        parent::__construct($name);
        $this->setValue($value);
    }
}
