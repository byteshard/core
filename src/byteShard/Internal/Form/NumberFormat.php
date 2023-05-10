<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Form;

/**
 * Trait NumberFormat
 * @package byteShard\Form\Internal
 * @property array $attributes
 */
trait NumberFormat
{
    /**
     * sets the format of numeric data
     * e.g. '0,000.00' | '$ 0.00'
     * @param string $string
     * @param string $decimalSeparator
     * @param string $thousandsSeparator
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setNumberFormat(string $string, string $decimalSeparator = ',', string $thousandsSeparator = '.'): self
    {
        if (isset($this->attributes)) {
            $this->attributes['numberFormat'] = $string;
            $this->attributes['groupSep']     = $thousandsSeparator;
            $this->attributes['decSep']       = $decimalSeparator;
        }
        return $this;
    }

    /**
     * a mark that will be used to divide numbers with many digits into groups. By default - '.' (dot);
     * @param string $thousandsSeparator
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setNumberFormatGroupSeparator(string $thousandsSeparator): self
    {
        trigger_error(__METHOD__.' is deprecated. Use setNumberFormatThousandsSeparator() instead', E_USER_DEPRECATED);
        return $this->setNumberFormatThousandsSeparator($thousandsSeparator);
    }

    /**
     * a mark that will be used to divide numbers with many digits into groups. By default - '.' (dot);
     * @param string $thousandsSeparator
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setNumberFormatThousandsSeparator(string $thousandsSeparator): self
    {
        if (isset($this->attributes)) {
            $this->attributes['groupSep'] = $thousandsSeparator;
        }
        return $this;
    }

    /**
     * a mark that will be used as the decimal delimiter. By default - ',' (comma).
     * @param string $decimalSeparator
     * @return $this
     * @noinspection PhpDocSignatureInspection
     * @API
     */
    public function setNumberFormatDecimalSeparator(string $decimalSeparator): self
    {
        if (isset($this->attributes)) {
            $this->attributes['decSep'] = $decimalSeparator;
        }
        return $this;
    }
}
