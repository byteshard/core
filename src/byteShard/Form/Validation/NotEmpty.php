<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Validation;

use byteShard\Internal\Validation;

/**
 * Class NotEmpty
 * @package byteShard\Validation
 */
class NotEmpty extends Validation
{
    /**
     * @var string DHTMLX inbuilt validation rule
     */
    protected string $clientValidation = 'NotEmpty';
}
