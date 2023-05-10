<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal;

use byteShard\ID\ID;

interface ContainerInterface
{
    /**
     * @return mixed
     */
    public function getID(): mixed;

    /**
     * @return string
     */
    public function getScopeLocaleToken(): string;

    public function getActionId(): string;

    public function getNonce(): string;

    public function getNewId(): ?ID;

    public function getContentClass(): string;
}
