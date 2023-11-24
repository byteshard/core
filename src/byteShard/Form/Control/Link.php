<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;

use byteShard\Enum\LinkTarget;
use byteShard\Internal\Form;

/**
 * Class Link
 * @package byteShard\Form\Control
 */
class Link extends Form\FormObject implements Form\InputWidthInterface, Form\ValueInterface
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

    private string     $href;
    private LinkTarget $target = LinkTarget::BLANK;

    public function __construct(string $id, string $href = '')
    {
        parent::__construct($id);
        $this->href = $href;
    }

    /**
     * @API
     */
    public function setHref(string $string): self
    {
        $this->href = $string;
        return $this;
    }

    /**
     * @API
     */
    public function setTarget(LinkTarget $target): self
    {
        $this->target = $target;
        return $this;
    }

    public function getValue(): string
    {
        $value = $this->attributes['value'] ?? '';
        if (!empty($this->href)) {
            return $value.'^'.$this->href;
        }
        return $value;
    }

    public function getFormat(): string
    {
        return match ($this->target) {
            LinkTarget::PARENT => 'bs_parent_link',
            LinkTarget::SELF   => 'bs_self_link',
            LinkTarget::TOP    => 'bs_top_link',
            LinkTarget::BLANK  => 'bs_blank_link',
        };
    }
}
