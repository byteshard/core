<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Enum;

/**
 * Class UploadMode
 * @package byteShard\Form\Enum
 */
enum UploadMode: string
{
    case HTML5 = 'html5';
    case HTML4 = 'html4';
    case FLASH = 'flash';
    case SL    = 'sl';
}
