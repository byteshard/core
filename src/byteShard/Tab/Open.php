<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Tab;

use byteShard\Exception;
use byteShard\Internal\LayoutContainer;
use byteShard\Database;
use byteShard\Session;

/**
 * Class Open
 * @package byteShard\Tab
 */
abstract class Open
{
    protected string  $data;
    protected ?string $name = null;
    protected mixed   $id;

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function isValid(): bool
    {
        return true;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getID(): mixed
    {
        return $this->id;
    }

    /**
     * @API
     * @throws Exception
     */
    protected function _openTabOnDB(string $table, string $field, string $id): void
    {
        $userId = Session::getUserId();
        if ($userId !== null) {
            $parameters = ['field' => $id, 'userId' => $userId];
            $record     = Database::getSingle('SELECT '.$field.' FROM '.$table.' WHERE '.$field.'=:field AND User_ID=:userId', $parameters);
            if ($record === null) {
                /** @noinspection SqlNoDataSourceInspection */
                Database::insert('INSERT INTO '.$table.' ('.$field.', User_ID, Active) VALUES (:field, :userId, 1)', $parameters);
            } else {
                Database::update('UPDATE '.$table.' SET Active=1 WHERE '.$field.'=:field AND User_ID=:userId', $parameters);
            }
        }
    }

    abstract public function getResult(LayoutContainer &$parentTab): array;
}
