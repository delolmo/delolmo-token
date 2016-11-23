<?php

namespace DelOlmo\Token\Storage\Db;

use DelOlmo\Token\Exception\DbColumnNotFoundException;
use DelOlmo\Token\Exception\DbTableNotFoundException;
use DelOlmo\Token\Exception\TokenNotFoundException;
use DelOlmo\Token\Storage\TokenStorageInterface;
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
        if (!$schema->tablesExist([$table])) {
            $str = "'%s' does not appear to be an existing table name in the "
                    . "given database.";
            $message = sprintf($str, $table);
            throw new DbTableNotFoundException($message);
        }

        // List all the columns for the given table
        $columns = $schema->listTableColumns($table);
        $columnsList = [];
        foreach($columns as $column) {
            $columnsList[] = $column->getName();
        }

        // Check for the validity of the 'id' column
        if (!in_array($id, $columnsList)) {
            $str = "'%s' does not appear to be an existing column name in "
                    . "the table '%s'";
            $message = sprintf($str, $id, $table);
            throw new DbColumnNotFoundException($message);
        }

        // Check the validity of the 'value' column
        if (!in_array($value, $columnsList)) {
            $str = "'%s' does not appear to be an existing column name in "
                    . "the table '%s'";
            $message = sprintf($str, $id, $table);
            throw new DbColumnNotFoundException($message);
        }

        // Everything went ok, store the values
        $this->connection = $connection;
        $this->table = $table;
        $this->columns['id'] = $id;
        $this->columns['value'] = $value;
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

        $table = $this->connection->quoteIdentifier($this->table);
        $idCol = $this->connection->quoteIdentifier($this->columns['id']);
        $sql = "SELECT * FROM {$table} WHERE {$idCol} = :id";

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
        $table = $this->connection->quoteIdentifier($this->table);
        $idCol = $this->connection->quoteIdentifier($this->columns['id']);
        $sql = "SELECT * FROM {$table} WHERE {$idCol} = :id";

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

        $table = $this->connection->quoteIdentifier($this->table);
        $idCol = $this->connection->quoteIdentifier($this->columns['id']);
        $sql = "DELETE {$table} WHERE {$idCol} = :id";

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
        $table = $this->connection->quoteIdentifier($this->table);
        $idCol = $this->connection->quoteIdentifier($this->columns['id']);
        $valueCol = $this->connection->quoteIdentifier($this->columns['value']);
        
        if ($this->hasToken($tokenId)) {
            $sql = "UPDATE {$table} SET {$valueCol} = :value WHERE {$idCol} = :id";
        } else {
            $sql = "INSERT INTO {$table} ({$idCol}, {$valueCol}) VALUES (:id, :value)";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $tokenId);
        $stmt->bindValue('value', $value);
        $stmt->execute();
    }

}
