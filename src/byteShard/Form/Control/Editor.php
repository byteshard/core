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
class Editor extends Form\FormObject implements Form\InputWidthInterface
{
    const H1 = 'applyH1';
    const H2 = 'applyH2';
    const H3 = 'applyH3';
    const H4 = 'applyH4';
    const BOLD = 'applyBold';
    const ITALIC = 'applyItalic';
    const UNDERSCORE = 'applyUnderscore';
    const STRIKETHROUGH = 'applyStrikethrough';
    const LEFT = 'alignLeft';
    const CENTER = 'alignCenter';
    const RIGHT = 'alignRight';
    const JUSTIFY = 'alignJustify';
    const SUB = 'applySub';
    const SUPER = 'applySuper';
    const ORDERED_LIST = 'createNumList';
    const UNORDERED_LIST = 'createBulList';
    const INDENT = 'increaseIndent';
    const UNINDENT = 'decreaseIndent';
    const CLEAR = 'clearFormatting';
    protected string $type                     = 'editor';
    protected ?string $dbColumnType           = Enum\DB\ColumnType::VARCHAR;
    protected string $displayedTextAttribute = 'label';
    use Form\ClassName;
    use Form\Disabled;

    //use Form\Help;
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

    //use Form\MaxLength;
    use Form\Name;
    use Form\Note;

    //use Form\NumberFormat;
    use Form\OffsetLeft;
    use Form\OffsetTop;
    use Form\Placeholder;
    use Form\Position;

    use Form\Required;


    use Form\Tooltip;
    use Form\Userdata;
    use Form\Validate;
    use Form\Value;

    public function setToolbar(string $iconPath = ''): Editor
    {
        $this->attributes['toolbar'] = true;
        if ($iconPath === '') {
            $this->attributes['iconsPath'] = 'bs/img/editor';
        } else {
            $this->attributes['iconsPath'] = $iconPath;
        }
        return $this;
    }

    public function setEditorControls(string ...$controls): Editor
    {
        $this->parameters['afterDataLoading']['editor'] = $controls;
        return $this;
    }
}
