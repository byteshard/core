<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait AutoWidth
 * @package byteShard\Form\Internal
 * @property array $parameters
 */
trait AutoWidth
{
    /**
     * this will automatically resize the form object to the form width minus the delta
     * @param int $delta = -27
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setAutoWidth(int $delta = -27): self
    {
        if (property_exists($this, 'parameters')) {
            $this->parameters['afterDataLoading']['setAutoWidth'] = $delta;
        }
        return $this;
    }
}
