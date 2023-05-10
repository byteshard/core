<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait CssName
 * @package byteShard\Form\Internal
 * @property string $cssName
 */
trait CssName
{
    protected string $cssName;

    /**
     * allows setting individual look for a specific FormObjects instance (see details below)
     * @param string $string
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setCssName(string $string): self
    {
        $this->cssName = $string;
        return $this;
    }
}
