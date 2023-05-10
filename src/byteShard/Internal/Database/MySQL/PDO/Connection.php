<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Database\MySQL\PDO;

use byteShard\Exception;
use byteShard\Internal\Database\MySQL\Connection as MySQLConnection;
use PDO;
use PDOException;

/**
 * Class Connection
 * @exceptionId 11031
 * @package byteShard\Internal\Database\MySQL
 */
class Connection extends MySQLConnection
{
    protected ?int $port = 3306;

    /**
     * function to create PDO connection
     * @throws Exception
     */
    public function connect(): void
    {
        try {
            $this->connection = new PDO('mysql:dbname='.$this->db.';host='.$this->server, $this->user, $this->pass);
        } catch (PDOException $e) {
            throw new Exception('Connection failed : '.$e->getMessage(), 110310001);
        }
        parent::connect();
    }

    /**
     * @param string $query
     * @return bool
     * @throws Exception
     */
    public function execute(string $query): bool
    {
        if ($this->connected === false) {
            $this->connect();
        }
        try {
            return $this->connection->query($query) !== false;
        } catch (PDOException $e) {
            throw new Exception('Error occurred : '.$e->getMessage().'<br>Executing query: '.$query, 110320013);
        }
    }
}