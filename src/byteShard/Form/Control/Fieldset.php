<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;
use byteShard\Internal\Form;

/**
 * Class Fieldset
 * @package byteShard\Form\Control
 */
class Fieldset extends Form\FormObject
{
    protected string $type = 'fieldset';
    protected string $displayedTextAttribute = 'label';
    use Form\ClassName;
    use Form\Disabled;
    use Form\Hidden;
    use Form\InputLeft;
    use Form\InputTop;
    use Form\Label;
    use Form\Nested;
    use Form\OffsetLeft;
    use Form\OffsetTop;
    use Form\Position;
    use Form\Width;
    use Form\Userdata;
}
