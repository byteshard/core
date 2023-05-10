<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Validation;

use byteShard\Internal\Validation;

/**
 * Class ValidSIN
 * @package byteShard\Validation
 */
class ValidSIN extends Validation
{
    /**
     * @var string DHTMLX inbuilt validation rule
     */
    protected string $clientValidation = 'ValidSIN';
}
