<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Control;

use byteShard\Internal\Form;

/**
 * Class Button
 * @package byteShard\Form\Control
 */
class Button extends Form\FormObject implements Form\ButtonInterface, Form\ValueInterface
{
    protected string $type                   = 'button';
    protected string $displayedTextAttribute = 'value';
    use Form\ClassName;
    use Form\Disabled;
    use Form\Hidden;
    use Form\InputLeft;
    use Form\InputTop;
    use Form\Name;
    use Form\OffsetLeft;
    use Form\OffsetTop;
    use Form\Position;
    use Form\Tooltip;
    use Form\Userdata;
    use Form\Value;
    use Form\Width;

    public function setRequiresSuccessfulValidation(): static
    {
        $this->setUserdata(['requiresSuccessfulValidation' => 'true']);
        return $this;
    }

    public function showLoader(): static
    {
        $this->setUserdata(['showLoader' => 'this']);
        return $this;
    }
}
