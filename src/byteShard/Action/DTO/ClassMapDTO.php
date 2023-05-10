<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Action\DTO;

class ClassMapDTO
{
    private readonly array $ids;

    public function __construct(public readonly string $className, IdMapDTO ...$ids)
    {
        $this->ids = $ids;
    }

    public function getIdString(): string
    {
        $array = [];
        foreach ($this->ids as $id) {
            $array[$id->idName] = $id->id;
        }
        ksort($array);
        $json = json_encode($array);
        return $json === false ? '' : $json;
    }
}
