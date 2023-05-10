<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;

use byteShard\Internal\Form\FormObject;
use byteShard\Internal\Form;

class Pdf extends FormObject
{
    use Form\ClassName;
    use Form\Disabled;
    use Form\Hidden;
    use Form\Label;
    use Form\LabelAlign;
    use Form\LabelHeight;
    use Form\LabelLeft;
    use Form\LabelTop;
    use Form\LabelWidth;
    use Form\Name;

    protected string $type = 'html';

    public function __construct(?string $id, string $url) {
        parent::__construct($id);
        $this->attributes['value'] = '<object class="pdfFormObject" data="'.$url.'" type="application/pdf">';
    }

    public function setValue(string $value): self
    {
        $this->attributes['value'] = '<object class="pdfFormObject" data="'.$value.'" type="application/pdf">';
        return $this;
    }
}
