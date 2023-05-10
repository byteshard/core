<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard;

class Model
{
    /**
     * @throws Exception
     */
    public function getApplicationSetting(string $setting): string|int
    {
        $setting = Database::getSingle("SELECT Value FROM tbl_Setting WHERE Setting='".$setting."'");
        if ($setting === null) {
            return '';
        }
        return $setting->Value ?? '';
    }

    /**
     * @param string $setting
     * @param string|int|null $value
     * @throws \Exception
     */
    public function storeApplicationSetting(string $setting, string|int|null $value): void
    {
        $check = Database::getSingle('SELECT Setting FROM tbl_Setting WHERE Setting=:setting', ['setting' => $setting]);
        if ($check === null) {
            Database::insert('INSERT INTO tbl_Setting (Setting, Value) VALUES (:setting, :value)', ['setting' => $setting, 'value' => $value]);
        } else {
            Database::update('UPDATE tbl_Setting SET Value=:value WHERE Setting=:setting', ['setting' => $setting, 'value' => $value]);
        }
    }
}
