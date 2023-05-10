<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;
use byteShard\Internal\Form;

/**
 * Class LabelAdvanced
 * @package byteShard\Form\Control
 */
class LabelAdvanced extends Form\FormObject
{
    protected string $type = 'label_advanced';
    protected string $displayedTextAttribute = 'label';
    use Form\ClassName;
    use Form\Disabled;
    use Form\FontWeight;
    use Form\Hidden;
    use Form\Info;
    use Form\Label;
    use Form\LabelHeight;
    use Form\LabelLeft;
    use Form\LabelTop;
    use Form\LabelWidth;
    use Form\Name;
    use Form\OffsetLeft;
    use Form\OffsetTop;
    use Form\Position;
    use Form\Userdata;

    public function __construct($name, $label = null) {
        parent::__construct($name);
        if ($label !== null) {
            $this->setLabel($label);
        }
    }
}
