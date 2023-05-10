<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Action;

/**
 * Class ReloadCell
 * @package byteShard\Action
 */
class ReloadCell extends Cell\ReloadCell
{
    public function __construct(string ...$cells) {
        trigger_error('byteShard\Action\ReloadCell is deprecated. Please update namespace to byteShard\Action\Cell\ReloadCell', E_USER_DEPRECATED);
        parent::__construct(...$cells);
    }
}
