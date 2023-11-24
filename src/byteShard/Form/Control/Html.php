<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;
use byteShard\Internal\Form;
/**
 * Class Html
 * @package byteShard\Form\Control
 */
class Html extends Form\FormObject implements Form\ValueInterface
{
    protected string $type = 'html';
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
    use Form\Value;
}
