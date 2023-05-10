<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

interface CollectionInterface
{
    /**
     * @return FormObject[]
     */
    public function getElements(): array;
}
