<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Validation;

use byteShard\Internal\Validation;

/**
 * Class MinLength
 * @package byteShard\Form\Validation
 */
class MinLength extends Validation
{
    private int $minLength;

    /**
     * @var string custom validation defined in dhtmlxValidation_custom: dhtmlxValidation.isMinLength
     */
    protected string $clientValidation = 'minLength';

    /**
     * MinLength constructor.
     * @param int $length
     */
    public function __construct(int $length)
    {
        $this->minLength = $length;
    }

    /**
     * @return string
     */
    public function getRule(): string
    {
        return 'minLength';
    }

    /**
     * @return bool|int
     */
    public function getValue(): bool|int
    {
        return $this->minLength;
    }

    /**
     * @return array
     */
    public function getUserData(): array
    {
        return ['bs_minlength' => $this->minLength];
    }
}
