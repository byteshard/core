<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait TitleText
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait TitleText
{
    /**
     * the text of the initial screen. The default value - "Drag-n-Drop files here or click to select files for upload"
     * @param string $text
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setTitleText(string $text): self
    {
        if (isset($this->attributes)) {
            $this->attributes['titleText'] = $text;
        }
        return $this;
    }
}
