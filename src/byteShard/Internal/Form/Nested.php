<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Cell;
use byteShard\Form\Control\Combo;

/**
 * Trait Nested
 * @package byteShard\Internal\Form
 * @property array $nestedControls
 */
trait Nested
{
    /**
     * @param FormObject ...$formObjects
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function addFormObject(FormObject ...$formObjects): self
    {
        foreach ($formObjects as $formObject) {
            if ($this->cell instanceof Cell && $formObject instanceof Combo) {
                $formObject->setSelectedID($this->cell->getContentSelectedID($formObject->getName()));
            }
            $this->nestedControls[] = $formObject;
        }
        return $this;
    }

    public function setNestedSelectedIds(): void
    {
        if ($this->cell instanceof Cell && property_exists($this, 'nestedControls') && is_array($this->nestedControls)) {
            foreach ($this->nestedControls as $nestedControl) {
                if ($nestedControl instanceof Combo) {
                    $nestedControl->setSelectedID($this->cell->getContentSelectedID($nestedControl->getName()));
                }
            }
        }
    }
}
