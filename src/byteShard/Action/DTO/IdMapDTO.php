<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Action\DTO;

class IdMapDTO
{
    /**
     * @param string $idName for example 'userId'
     * @param string|int $id for example 5
     */
    public function __construct(
        public readonly string     $idName,
        public readonly string|int $id
    )
    { }
}
