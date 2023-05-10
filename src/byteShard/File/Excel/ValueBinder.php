<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\File\Excel;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class ValueBinder extends DefaultValueBinder
{
    /**
     * DataType for value.
     *
     * @param mixed $value
     *
     * @return string
     */
    public static function dataTypeForValue(mixed $value): string
    {
        // Match the value against a few data types
        if ($value === null) {
            return DataType::TYPE_NULL;
        } elseif (is_float($value) || is_int($value)) {
            return DataType::TYPE_NUMERIC;
        } elseif (is_bool($value)) {
            return DataType::TYPE_BOOL;
        } elseif (is_int($value)) {
            return DataType::TYPE_NUMERIC;
        } elseif ($value === '') {
            return DataType::TYPE_STRING;
        } elseif ($value instanceof RichText) {
            return DataType::TYPE_INLINE;
        } elseif (is_string($value) && $value[0] === '=' && strlen($value) > 1) {
            return DataType::TYPE_FORMULA;
        } elseif (is_string($value)) {
            return DataType::TYPE_STRING;
        } elseif (preg_match('/^[\+\-]?(\d+\\.?\d*|\d*\\.?\d+)([Ee][\-\+]?[0-2]?\d{1,3})?$/', $value)) {
            $tValue = ltrim($value, '+-');
            if (is_string($value) && $tValue[0] === '0' && strlen($tValue) > 1 && $tValue[1] !== '.') {
                return DataType::TYPE_STRING;
            } elseif ((!str_contains($value, '.')) && ($value > PHP_INT_MAX)) {
                return DataType::TYPE_STRING;
            }
            return DataType::TYPE_NUMERIC;
        } elseif (is_string($value)) {
            $errorCodes = DataType::getErrorCodes();
            if (isset($errorCodes[$value])) {
                return DataType::TYPE_ERROR;
            }
        }

        return DataType::TYPE_STRING;
    }
}
