<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard;

use byteShard\Internal\CellContent;

//use byteShard\Internal\Permission\PermissionImplementation;

abstract class Accordion extends CellContent
{

    protected string $cellContentType = 'DHTMLXAccordion';

    /**
     * @session write
     * @session read
     * @return array
     * @internal
     */
    public function getCellContent(array $content = array()): array
    {
        $parent_content = parent::getCellContent(array());
        //'content'           => $this->getXML(),
        //'contentEvents'     => $this->getCellEvents(),
        //'contentParameters' => $this->getCellParameters(),
        $test = array_merge($parent_content, array(
            'cells'         => array(array('id' => 'a1', 'text' => 'Foo'), array('id' => 'a2', 'text' => 'Bar'), array('id' => 'a3', 'text' => 'Baz')),
            'contentType'   => $this->cellContentType,
            'contentFormat' => $this->cell->getContentFormat()));
        return $test;
    }
    //private $cell;

    //use PermissionImplementation;

    /*private $cells;
    public function __construct(Cell ...$cells)
    {
        $this->cells = $cells;
    }

    public function setParentAccessType($access_type) {
        return $this;
    }

    public function getNavigationData(Session $session = null) {
        $cellData['label'] = 'Nope';
        $cellData['toolbar'] = false;
        return $cellData;
    }

    public function getHorizontalAutoSize() {
        if (isset($this->cell['width'])) {
            return false;
        }
        return true;
    }

    public function getVerticalAutoSize() {
        if (isset($this->cell['height'])) {
            return false;
        }
        return true;
    }*/
}
