<?php

namespace DelOlmo\Token\Storage\Db;

use DelOlmo\Token\Exception\DbColumnNotFoundException;
use DelOlmo\Token\Exception\DbTableNotFoundException;
use DelOlmo\Token\Exception\TokenNotFoundException;
use Doctrine\DBAL\Connection;

/**
 * Description of DoctrineDbalTokenStorage
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class DoctrineDbalTokenStorage implements TokenStorageInterface
{

    /**
     * @var string
     */
    protected $connection;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * Constructor
     * 
     * @param Connection $connection
     * @param array $options
     * @throws \DelOlmo\Token\Exception\DbTableNotFoundException if the given
     * table does not exist
     * @throws \DelOlmo\Token\Exception\DbColumnNotFoundException if the given
     * column names do not exist
     */
    public function __construct(Connection $connection, array $options)
    {
        $table = $options['table'] ?? 'tokens';
        $id = $options['columns']['id'] ?? 'id';
        $value = $options['columns']['value'] ?? 'value';

        // Get the schema manager to inspect the database
        $schema = $connection->getSchemaManager();

        // Check for the validity of the table name
        if (!$schema - tablesExist([$this->table])) {
            $str = "'%s' does not appear to be an existing table name in the "
                    . "given database.";
            $message = sprintf($str, $this->table);
            throw new DbTableNotFoundException($message);
        }

        // List all the columns for the given table
        $columns = $schema->listTableColumns($this->table);

        // Check for the validity of the 'id' column
        if (!in_array($id, $columns)) {
            $str = "'%s' does not appear to be an existing column name in "
                    . "the table '%s'";
            $message = sprintf($str, $id, $this->table);
            throw new DbColumnNotFoundException($message);
        }

        // Check the validity of the 'value' column
        if (!in_array($value, $columns)) {
            $str = "'%s' does not appear to be an existing column name in "
                    . "the table '%s'";
            $message = sprintf($str, $id, $this->table);
            throw new DbColumnNotFoundException($message);
        }

        // Everything went ok, store the values
        $this->connection = $connection;
        $this->table = $connection->quoteIdentifier($table);
        $this->columns['id'] = $connection->quoteIdentifier($id);
        $this->columns['value'] = $connection->quoteIdentifier($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(string $tokenId): string
    {
        if (!$this->hasToken($tokenId)) {
            $str = "No valid token exists for the given id '%s'.";
            $message = sprintf($str, $tokenId);
            throw new TokenNotFoundException($message);
        }

        $sql = "SELECT * FROM {$this->table} "
                . "WHERE {$this->columns['id']} = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $tokenId);
        $stmt->execute();

        return $stmt->fetch()[$this->columns['value']];
    }

    /**
     * {@inheritdoc}
     */
    public function hasToken(string $tokenId): bool
    {
        $sql = "SELECT * FROM {$this->table} "
                . "WHERE {$this->columns['id']} = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $tokenId);
        $stmt->execute();

        return $stmt->rowCount() !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function removeToken(string $tokenId)
    {
        if (!$this->hasToken($tokenId)) {
            return null;
        }

        $token = $this->getToken($tokenId);

        $sql = "DELETE {$this->table} WHERE {$this->columns['id']} = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $tokenId);
        $stmt->execute();

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(string $tokenId, string $value)
    {
        if ($this->hasToken($tokenId)) {
            $sql = "UPDATE {$this->table} SET {$this->columns['value']} = "
                    . ":value WHERE {$this->columns['id']} = :id";
        } else {
            $sql = "INSERT INTO {$this->table} ({$this->columns['id']}, "
                    . "{$this->columns['value']}) VALUES (:id, :value)";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $tokenId);
        $stmt->bindValue('value', $value);
        $stmt->execute();
    }

}
