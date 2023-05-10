<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Enum\Label;

/**
 * Class LabelPosition
 * @package byteShard\Form\Enum\Label
 */
enum Position: string
{
    case ABSOLUTE = 'absolute';
    case LEFT     = 'label-left';
    case RIGHT    = 'label-right';
    case TOP      = 'label-top';
}
