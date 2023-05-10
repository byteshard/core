<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Action;

/**
 * Interface ControlIdInterface
 * @package byteShard\Internal\Action
 */
interface ControlIdInterface
{
    public function setControlID(string $id): self;
}
