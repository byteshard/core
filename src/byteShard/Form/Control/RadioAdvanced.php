<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;

use byteShard\Enum;
use byteShard\Internal\Form;

/**
 * Class Radio
 * @package byteShard\Form\Control
 */
class RadioAdvanced extends Form\FormObject implements Form\OnlyReadInterface
{
    protected string $type                     = 'radioAdvanced';
    protected ?string $dbColumnType           = Enum\DB\ColumnType::VARCHAR;
    protected string $displayedTextAttribute = 'label';

    use Form\Checked;
    use Form\ClassName;
    use Form\Disabled;
    use Form\Hidden;
    use Form\Info;
    use Form\InputLeft;
    use Form\InputTop;
    use Form\Label;
    use Form\LabelAlign;
    use Form\LabelHeight;
    use Form\LabelLeft;
    use Form\LabelTop;
    use Form\LabelWidth;
    use Form\Nested;
    use Form\Name;
    use Form\Note;
    use Form\OffsetLeft;
    use Form\OffsetTop;
    use Form\Position;
    use Form\OnlyRead;
    use Form\Required;
    use Form\Tooltip;
    use Form\Userdata;
    use Form\Value;
}
