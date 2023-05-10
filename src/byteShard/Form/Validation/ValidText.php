<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Validation;

use byteShard\Internal\Validation;

/**
 * Class ValidText
 * @package byteShard\Validation
 */
class ValidText extends Validation
{
    /**
     * @var string custom validation defined in dhtmlxValidation_custom: dhtmlxValidation.isValidText
     */
    protected string $clientValidation = 'ValidText';
}
