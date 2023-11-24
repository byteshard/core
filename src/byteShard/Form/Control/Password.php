<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;

use byteShard\Internal\Form;
use byteShard\Enum;

/**
 * Class Password
 * @package byteShard\Form\Control
 */
class Password extends Form\FormObject implements Form\InputWidthInterface, Form\OnlyReadInterface, Form\ValueInterface
{
    protected string              $type                   = 'password';
    protected ?Enum\DB\ColumnType $dbColumnType           = Enum\DB\ColumnType::VARCHAR;
    protected string              $displayedTextAttribute = 'label';
    use Form\ClassName;
    use Form\Disabled;
    use Form\Hidden;
    use Form\Info;
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
    use Form\MaxLength;
    use Form\Name;
    use Form\Note;
    use Form\OffsetLeft;
    use Form\OffsetTop;
    use Form\Position;
    use Form\OnlyRead;
    use Form\Required;
    use Form\Style;
    use Form\Tooltip;
    use Form\Userdata;
    use Form\Validate;
    use Form\Value;
}
