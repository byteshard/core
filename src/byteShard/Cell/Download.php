<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Cell;

use byteShard\File\FileInterface;

/**
 * Interface Download
 * @API
 * @package byteShard\Cell
 */
interface Download
{
    /**
     * @API
     * @return FileInterface|null
     */
    public function defineDownload(): ?FileInterface;
}
