<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Struct;

use Iterator;

class Rows implements Iterator
{
    /** @var bool */
    private bool $eof = true;
    /** @var Row[] */
    private array $rows = [];
    /** @var ClientData */
    private ClientData $parent;
    /** @var int */
    private int $position = 0;

    public function __construct(ClientData $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @param null $index
     * @return Row
     */
    public function addRow($index = null): Row
    {
        if ($index === null) {
            $row          = new Row($this);
            $this->rows[] = $row;
        } else {
            if (array_key_exists($index, $this->rows) === false) {
                $row                = new Row($this);
                $this->rows[$index] = $row;
            } else {
                $row = $this->rows[$index];
            }
        }
        if (count($this->rows) > 1) {
            $this->eof = false;
        }
        return $row;
    }

    /**
     * @return bool
     */
    public function eof(): bool
    {
        return $this->eof;
    }

    /**
     * @return Row[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @param string $name
     * @param Data $data
     * @internal
     */
    public function setRowData(string $name, Data $data): void
    {
        if ($this->eof === true) {
            $this->parent->setRowData($name, $data);
        }
    }

    /**
     * @inheritDoc
     */
    public function current(): mixed
    {
        return $this->rows[$this->position];
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * @inheritDoc
     */
    public function key(): mixed
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return isset($this->rows[$this->position]);
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->position = 0;
    }
}
