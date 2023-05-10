<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\ClientData;

class ProcessedClientData implements ProcessedClientDataInterface
{
    public function __construct(
        public mixed  $value = null,
        public string $id = '',
        public string $objectType = '',
        public int    $accessType = 0,
        public bool   $encryptedImplementation = false,
        public array  $failedValidationMessages = [],
        public string $clientId = '',
        public string $label = '',
        public bool   $preexistingComboOption = false
    ) {
    }
}