<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\ID;

use byteShard\Session;

class RowID
{
    private string $rowId;
    public function __construct(array $rowId)
    {
        ksort($rowId);
        $this->rowId = json_encode($rowId);
    }

    public function getEncodedRowId(): string
    {
        return $this->rowId;
    }

    public function getEncryptedRowId(string $cellNonce): string
    {
        $nonce = substr(md5($cellNonce.$this->rowId), 0, 24);
        return Session::encrypt($this->rowId, $nonce);
    }

    public static function encrypt(array $rowIds, string $cellNonce): string
    {
        $rowIdObject = new self($rowIds);
        return $rowIdObject->getEncryptedRowId($cellNonce);
    }
}
