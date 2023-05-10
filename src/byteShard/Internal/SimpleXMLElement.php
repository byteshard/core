<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal;

use SimpleXMLElement as NativeSimpleXmlElement;

/**
 * Class SimpleXMLElement
 * @package byteShard\Internal
 */
class SimpleXMLElement extends NativeSimpleXmlElement
{
    public function __construct(string $data, int $options = 0, bool $dataIsURL = false, string $namespaceOrPrefix = '', bool $isPrefix = false)
    {
        parent::__construct($data, $options, $dataIsURL, $namespaceOrPrefix, $isPrefix);
        Debug::error(__CLASS__.' is deprecated');
    }
}
