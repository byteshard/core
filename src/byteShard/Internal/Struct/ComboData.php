<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Struct;

class ComboData extends Data
{
    public bool $preexistingComboOption = false;
    public function __construct($value, ?string $type, bool $preexistingComboOption = false) {
        parent::__construct($value, $type);
        $this->preexistingComboOption = $preexistingComboOption;
    }
}