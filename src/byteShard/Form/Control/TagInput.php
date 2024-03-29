<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;

use byteShard\Internal\Form;
use byteShard\Enum;

/**
 * Class TagInput
 * @package byteShard\Form\Control
 */
class TagInput extends Form\FormObject implements Form\InputWidthInterface, Form\OnlyReadInterface
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

    public function __construct(?string $id, array $tags = [])
    {
        parent::__construct($id);
        $this->setTags($tags);
    }

    public function setValue(array $value): self
    {
        if (!empty($value)) {
            $arr = [];
            foreach ($value as $val) {
                $arr[] = (object)['value' => $val];
            }
            $this->attributes['value'] = json_encode($arr);
        } else {
            $this->attributes['value'] = '';
        }
        return $this;
    }

    public function setTags(array $tags): self
    {
        $this->parameters['afterDataLoading']['tagify'] = $tags;
        return $this;
    }
}
