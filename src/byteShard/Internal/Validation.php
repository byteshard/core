<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal;

/**
 * Class Validation
 * @package byteShard\Internal
 */
abstract class Validation
{
    /**
     * @var string
     * @deprecated
     */
    protected string $clientValidation = '';

    /**
     * @return string
     */
    public function getClientValidation(): string
    {
        return $this->clientValidation;
    }

    /**
     * @return array
     */
    public function getUserData(): array
    {
        return [];
    }

    /**
     * deprecated
     * @return string
     */
    public function getRule(): string
    {
        return $this->clientValidation;
    }

    /**
     * deprecated
     * @return bool
     */
    public function getValue(): bool|int
    {
        return true;
    }
}
