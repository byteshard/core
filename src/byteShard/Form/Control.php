<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form;

use byteShard\Form\Control\Checkbox;
use byteShard\Form\Control\Input;
use byteShard\Form\Control\Textarea;

class Control
{
    public static function Input(string $id): Input
    {
        return new Input($id);
    }

    public static function Textarea(string $id): Textarea
    {
        return new Textarea($id);
    }

    public static function Checkbox(string $id): Checkbox
    {
        return new Checkbox($id);
    }
}
