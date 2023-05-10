<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form\Validation;

use byteShard\Internal\Validation;

/**
 * Class MaxLength
 * @package byteShard\Form\Validation
 */
class MaxLength extends Validation
{
    private int $maxLength;

    /**
     * @var string custom validation defined in dhtmlxValidation_custom: dhtmlxValidation.isMaxLength
     */
    protected string $clientValidation = 'maxLength';

    /**
     * MaxLength constructor.
     * @param int $length
     */
    public function __construct(int $length)
    {
        $this->maxLength = $length;
    }

    /**
     * @return string
     */
    public function getRule(): string
    {
        return 'maxLength';
    }

    /**
     * @return bool|int
     */
    public function getValue(): bool|int
    {
        return $this->maxLength;
    }

    /**
     * @return array
     */
    public function getUserData(): array
    {
        return ['bs_maxlength' => $this->maxLength];
    }
}
