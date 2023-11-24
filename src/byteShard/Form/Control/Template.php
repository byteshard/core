<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;

use byteShard\Internal\Form;

/**
 * Class Template
 * @package byteShard\Form\Control
 */
class Template extends Form\FormObject implements Form\InputWidthInterface, Form\ValueInterface
{
    protected string $type                   = 'template';
    protected string $displayedTextAttribute = 'label';
    use Form\ClassName;
    use Form\Info;
    use Form\Format;
    use Form\Hidden;
    use Form\InputHeight;
    use Form\InputWidth;
    use Form\InputLeft;
    use Form\InputTop;
    use Form\Label;
    use Form\LabelAlign;
    use Form\LabelHeight;
    use Form\LabelLeft;
    use Form\LabelTop;
    use Form\LabelWidth;
    use Form\Name;
    use Form\Note;
    use Form\OffsetLeft;
    use Form\OffsetTop;
    use Form\Position;
    use Form\Required;
    use Form\Style;
    use Form\Tooltip;
    use Form\Value;
    use Form\Userdata;
}
