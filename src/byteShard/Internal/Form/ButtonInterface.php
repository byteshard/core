<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

use byteShard\Internal\Event\Event;

interface ButtonInterface
{
    public function addEvents(Event ...$events): static;

    public function setClassName(string $className): self;

    public function setRequiresSuccessfulValidation(): static;

    public function showLoader(): static;
}
