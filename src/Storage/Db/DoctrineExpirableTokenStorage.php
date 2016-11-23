<?php

namespace DelOlmo\Token\Storage\Db;

use DelOlmo\Token\Exception\DbColumnNotFoundException;
use DelOlmo\Token\Exception\DbTableNotFoundException;
use DelOlmo\Token\Exception\DbRuntimeException;
use DelOlmo\Token\Exception\TokenNotFoundException;
use DelOlmo\Token\Storage\TokenStorageInterface;
use Doctrine\DBAL\Connection;

/**
 * An object to store tokens using a Doctrine DBAL connection. The class
 * constructor requires (1)a valid database connection, (2) the name of the 
 * table where the tokens are stored, and (3) the names of the columns for each 
 * one of the token fields (id and value).
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class DoctrineExpirableTokenStorage implements TokenStorageInterface
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
        $expiresAt = $options['columns']['expiresAt'] ?? 'expires_at';

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
        foreach ($columns as $column) {
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
        
        // Check the validity of the 'expiresAt' column
        if (!in_array($expiresAt, $columnsList)) {
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
        $this->columns['expiresAt'] = $expiresAt;
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

        try {
            
            $connection = $this->connection;
            $table = $connection->quoteIdentifier($this->table);
            $idCol = $connection->quoteIdentifier($this->columns['id']);
            $sql = "SELECT * FROM {$table} WHERE {$idCol} = :id";

            $stmt = $connection->prepare($sql);
            $stmt->bindValue('id', $tokenId);
            $stmt->execute();
            $token = $stmt->fetch()[$this->columns['value']];
            
        } catch (\Exception $exception) {

            $str = "An unexpected exception was thrown while executing the "
                    . "following query: '%s'. The complete error message was: "
                    . "%s";
            $message = sprintf($str, $sql, $exception->getMessage());
            throw new DbRuntimeException($message);
        }

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function hasToken(string $tokenId): bool
    {
        try {
            
            $connection = $this->connection;
            $table = $connection->quoteIdentifier($this->table);
            $idCol = $connection->quoteIdentifier($this->columns['id']);
            $expiresAtCol = $connection->quoteIdentifier($this->columns['expiresAt']);
            $sql = "SELECT * FROM {$table} WHERE {$idCol} = :id AND "
                . "{$expiresAtCol} < :expires_at";

            $stmt = $connection->prepare($sql);
            $stmt->bindValue('id', $tokenId);
            $stmt->bindValue('expires_at', (new \DateTime())->format('Y-m-d H:i:s'));
            $stmt->execute();
            $rowCount = $stmt->rowCount();

        } catch (\Exception $exception) {

            $str = "An unexpected exception was thrown while executing the "
                    . "following query: '%s'. The complete error message was: "
                    . "%s";
            $message = sprintf($str, $sql, $exception->getMessage());
            throw new DbRuntimeException($message);
        }

        return $rowCount !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function removeToken(string $tokenId)
    {
        if (!$this->hasToken($tokenId)) {
            return null;
        }

        try {
            
            $token = $this->getToken($tokenId);
            
            $connection = $this->connection;
            $table = $connection->quoteIdentifier($this->table);
            $idCol = $connection->quoteIdentifier($this->columns['id']);
            $sql = "DELETE FROM {$table} WHERE {$idCol} = :id";
            
            $stmt = $connection->prepare($sql);
            $stmt->bindValue('id', $tokenId);
            $stmt->execute();
            
        } catch (\Exception $exception) {

            $str = "An unexpected exception was thrown while executing the "
                    . "following query: '%s'. The complete error message was: "
                    . "%s";
            $message = sprintf($str, $sql, $exception->getMessage());
            throw new DbRuntimeException($message);
        }

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(string $tokenId, string $value)
    {
        try {
            
            $connection = $this->connection;
            $table = $connection->quoteIdentifier($this->table);
            $idCol = $connection->quoteIdentifier($this->columns['id']);
            $valueCol = $connection->quoteIdentifier($this->columns['value']);

            $sql = $this->hasToken($tokenId) ?
                "UPDATE {$table} SET {$valueCol} = :value WHERE {$idCol} = :id" :
                "INSERT INTO {$table} ({$idCol}, {$valueCol}) VALUES (:id, :value)";

            $stmt = $connection->prepare($sql);
            $stmt->bindValue('id', $tokenId);
            $stmt->bindValue('value', $value);
            $stmt->execute();
            
        } catch (\Exception $exception) {

            $str = "An unexpected exception was thrown while executing the "
                    . "following query: '%s'. The complete error message was: "
                    . "%s";
            $message = sprintf($str, $sql, $exception->getMessage());
            throw new DbRuntimeException($message);
        }
    }

}
