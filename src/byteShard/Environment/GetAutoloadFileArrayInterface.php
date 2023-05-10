<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Environment;

interface GetAutoloadFileArrayInterface extends AutoloadInterface
{
    /**
     * @return array
     */
    public function getAutoloadFileArray(): array;
}
