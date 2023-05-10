<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

use byteShard\Chart\Property\Item as ItemObject;

trait Item
{
    private ItemObject $item;

    /** @API */
    public function setItem(ItemObject $item): self
    {
        $this->item = $item;
        return $this;
    }

    protected function getItem(): array
    {
        if (isset($this->item)) {
            return $this->item->getItem() ?? ['item' => null];
        }
        return ['item' => null];
    }
}
