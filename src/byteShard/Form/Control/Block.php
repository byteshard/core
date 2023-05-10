<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;
use byteShard\Internal\Form;
/**
 * Class Block
 * @package byteShard\Form\Control
 */
class Block extends Form\FormObject
{
    protected string $type = 'block';

    use Form\BlockOffset;
    use Form\ClassName;
    use Form\Disabled;
    use Form\Hidden;
    use Form\InputLeft;
    use Form\InputTop;
    use Form\Nested;
    use Form\Name;
    use Form\OffsetLeft;
    use Form\OffsetTop;
    use Form\Position;
    use Form\Width;

    public function __construct(Form\FormObject ...$form_objects) {
        parent::__construct(null);
        $this->addFormObject(...$form_objects);
    }
}
