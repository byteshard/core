<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;
use byteShard\Internal\Form;

/**
 * Class Colorpicker
 * @package byteShard\Form\Control
 */
class Colorpicker extends Form\FormObject
{
    protected string $type = 'colorpicker';
    protected string $displayedTextAttribute = 'label';
    use Form\Label;
}
