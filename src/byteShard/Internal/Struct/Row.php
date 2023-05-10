<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Struct;

use byteShard\Form\Control\Combo;
use Iterator;
use stdClass;

/**
 * Class Row
 * @package byteShard\Internal\Struct
 */
class Row extends stdClass implements Iterator
{
    /** @var Rows */
    private Rows  $parent;
    private int   $position   = 0;
    private array $iterFields = [];

    public function __construct(Rows $parent)
    {
        $this->parent = $parent;
    }

    public function addField(string $name, $value, ?string $type = null, bool $preexistingComboOption = false): void
    {
        if (property_exists($this, $name) === false) {
            if ($type === Combo::class || is_subclass_of($type, Combo::class)) {
                $this->{$name} = new ComboData($value, $type, $preexistingComboOption);
            } else {
                $this->{$name} = new Data($value, $type);
            }
            $this->iterFields[] = $name;
            // if we're on the
            if ($this->parent->eof() === true) {
                $this->parent->setRowData($name, $this->{$name});
            }
        }
    }

    public function getValue($name)
    {
        if (property_exists($this, $name) === true && property_exists($this->{$name}, 'value') === true) {
            return $this->{$name}->value;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function current(): mixed
    {
        return $this->{$this->iterFields[$this->position]};
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
        return $this->iterFields[$this->position];
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return isset($this->iterFields[$this->position]);
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        sort($this->iterFields);
        $this->position = 0;
    }
}
