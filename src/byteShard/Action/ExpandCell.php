<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Action;

class ExpandCell extends Cell\ExpandCell
{
    public function __construct(string ...$cells)
    {
        trigger_error('byteShard\Action\ExpandCell is deprecated. Please update namespace to byteShard\Action\Cell\ExpandCell', E_USER_DEPRECATED);
        parent::__construct(...$cells);
    }
}