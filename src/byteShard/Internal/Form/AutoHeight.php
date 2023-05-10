<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait AutoHeight
 * @package byteShard\Form\Internal
 * @property array $parameters
 */
trait AutoHeight
{
    /**
     * currently this does not work due to dhtmlx limitations
     * TODO: multiple objects with autoHeight need special considerations
     * @param int $delta
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setAutoHeight(int $delta = 0): self
    {
        if (property_exists($this, 'parameters')) {
            $this->parameters['afterDataLoading']['setAutoHeight'] = $delta;
        }
        return $this;
    }
}
