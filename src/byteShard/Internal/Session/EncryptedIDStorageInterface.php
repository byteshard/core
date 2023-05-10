<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Session;

interface EncryptedIDStorageInterface
{

    public function encryptID(string $id, ?int $level = null): string;

    public function getEncryptedIDs(): array;
}
