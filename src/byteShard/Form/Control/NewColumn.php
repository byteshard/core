<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;
use byteShard\Internal\Form;

/**
 * Class NewColumn
 * @package byteShard\Form\Control
 */
class NewColumn extends Form\FormObject
{
    protected string $type = 'newcolumn';
    use Form\Offset;
    use Form\Nested;
    use Form\Name;

    public function __construct($id = null) {
        parent::__construct($id);
    }
}
