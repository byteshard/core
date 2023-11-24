<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;

use byteShard\Internal\Form;
use byteShard\Enum;

/**
 * Class Input
 * @package byteShard\Form\Control
 */
class Input extends Form\FormObject implements Form\InputWidthInterface, Form\OnlyReadInterface, Form\ValueInterface
{
    protected string              $type                   = 'input';
    protected ?Enum\DB\ColumnType $dbColumnType           = Enum\DB\ColumnType::VARCHAR;
    protected string              $displayedTextAttribute = 'label';

    use Form\ClassName;
    use Form\Disabled;
    use Form\Help;
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
    use Form\NumberFormat;
    use Form\OffsetLeft;
    use Form\OffsetTop;
    use Form\Placeholder;
    use Form\Position;
    use Form\OnlyRead;
    use Form\Required;
    use Form\Rows;
    use Form\Style;
    use Form\Tooltip;
    use Form\Userdata;
    use Form\Validate;
    use Form\Value;

    /**
     * @param array $completions
     * @return $this
     */
    public function setAutoCompletions(array $completions): self
    {
        $this->parameters['afterDataLoading']['autoCompletion'] = $completions;
        return $this;
    }
}
