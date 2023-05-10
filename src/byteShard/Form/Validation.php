<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Form;

use byteShard\Enum;

/**
 * Class Validation
 * @package byteShard\Form
 */
class Validation extends \byteShard\Internal\Validation
{
    private array $validation          = [];
    private array $frameworkValidation = [];

    /**
     * Validation constructor.
     * @param null $arrayOfValidations
     */
    public function __construct($arrayOfValidations = null)
    {
        if (!is_null($arrayOfValidations)) {
            $this->addValidations($arrayOfValidations);
        }
    }

    /**
     * @param $array
     */
    public function addValidations($array): void
    {
        if (is_array($array)) {
            foreach ($array as $key => $validation) {
                if (is_numeric($key)) {
                    $this->_addValidation($validation);
                } elseif (Enum\Validation::is_enum($key)) {
                    switch ($key) {
                        case Enum\Validation::ENUM:
                            $this->setEnumType($validation);
                            break;
                        case Enum\Validation::MIN_LENGTH:
                            $this->setMinLength($validation);
                            break;
                        case Enum\Validation::MAX_LENGTH:
                            $this->setMaxLength($validation);
                            break;
                        default:
                            $this->_addValidation($key);
                            break;
                    }
                }
            }
        } else {
            $this->_addValidation($array);
        }
    }

    private function _addValidation($validation)
    {
        if (Enum\Validation::is_enum($validation)) {
            $this->validation[$validation] = true;
        }
    }

    /**
     * @param int $minLength
     * @return $this
     */
    public function setMinLength(int $minLength): self
    {
        $this->frameworkValidation['minLength'] = $minLength;

        return $this;
    }

    /**
     * @param int $maxLength
     * @return $this
     */
    public function setMaxLength(int $maxLength): self
    {
        $this->frameworkValidation['maxLength'] = $maxLength;
        return $this;
    }

    /**
     * @param string $enumClassName
     * @return $this
     */
    public function setEnumType(string $enumClassName): self
    {
        $this->frameworkValidation['enum'] = $enumClassName;
        return $this;
    }

    /**
     * @return array
     */
    public function getValidationArray(): array
    {
        return array_merge($this->frameworkValidation, $this->validation);
    }

    /**
     * @return $this
     */
    public function isEmpty(): self
    {
        $this->validation['Empty'] = true;
        return $this;
    }

    /**
     * @return $this
     * @API
     */
    public function notEmpty(): self
    {
        $this->validation['NotEmpty'] = true;
        return $this;
    }

    /**
     * @return $this
     * @API
     */
    public function validBoolean(): self
    {
        $this->validation['ValidBoolean'] = true;
        return $this;
    }

    /**
     * @return $this
     * @API
     */
    public function validEmail(): self
    {
        $this->validation['ValidEmail'] = true;
        return $this;
    }

    /**
     * @return $this
     * @API
     */
    public function validInteger(): self
    {
        $this->validation['ValidInteger'] = true;
        return $this;
    }

    /**
     * @return $this
     * @API
     */
    public function validNumeric(): self
    {
        $this->validation['ValidNumeric'] = true;
        return $this;
    }

    /**
     * @return $this
     * @API
     */
    public function validAlphaNumeric(): self
    {
        $this->validation['ValidText'] = true;
        return $this;
    }

    /**
     * @return $this
     * @API
     */
    public function validDatetime(): self
    {
        $this->validation['ValidDatetime'] = true;
        return $this;
    }

    /**
     * @return $this
     * @API
     */
    public function validDate(): self
    {
        $this->validation['ValidDate'] = true;
        return $this;
    }

    /**
     * @return $this
     * @API
     */
    public function validTime(): self
    {
        $this->validation['ValidTime'] = true;
        return $this;
    }

    /**
     * @return $this
     * @API
     */
    public function validIPv4(): self
    {
        $this->validation['ValidIPv4'] = true;
        return $this;
    }

    /**
     * @return $this
     * @API
     */
    public function validCurrency(): self
    {
        $this->validation['ValidCurrency'] = true;
        return $this;
    }

    /**
     * @return $this
     * @API
     */
    public function validSSN(): self
    {
        $this->validation['ValidSSN'] = true;
        return $this;
    }

    /**
     * @return $this
     * @API
     */
    public function validSIN(): self
    {
        $this->validation['ValidSIN'] = true;
        return $this;
    }

    //###################
    // Custom Validations
    //###################
    /**
     * @return $this
     * @API
     */
    public function validText(): self
    {
        $this->validation['ValidText'] = true;
        return $this;
    }

    /**
     * @return null|string
     * @API
     */
    public function getValidation(): ?string
    {
        if (!empty($this->validation)) {
            return implode(',', array_keys($this->validation));
        } else {
            return null;
        }
    }

    public function getClientValidation(): string
    {
        return '';
    }
}
