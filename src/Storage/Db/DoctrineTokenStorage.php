<?php

namespace DelOlmo\Token\Storage\Db;

use DelOlmo\Token\Exception\DbColumnNotFoundException;
use DelOlmo\Token\Exception\DbMissingColumnException;
use DelOlmo\Token\Exception\DbMissingTableException;
use DelOlmo\Token\Exception\DbTableNotFoundException;
use DelOlmo\Token\Exception\DbRuntimeException;
use DelOlmo\Token\Exception\TokenNotFoundException;
use DelOlmo\Token\Storage\TokenStorageInterface;
use Doctrine\DBAL\Connection;

/**
 * An object to store tokens using a Doctrine DBAL connection. The class
 * constructor requires (1) a valid database connection, (2) the name of the 
 * table where the tokens are stored, and (3) the names of the columns for each 
 * one of the token fields (id and value).
 *
 * @author Antonio del Olmo García <adelolmog@gmail.com>
 */
class DoctrineTokenStorage implements TokenStorageInterface
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
     * @var array An array holding all the required parameters
     */
    protected static $parameters = null;

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
        // Check that the provided arguments are correct
        self::checkStorageOptions($connection, $options);

        // Everything went ok, store the values
        $this->connection = $connection;
        $this->table = $options['table'];

        // Save parameters
        foreach (self::getTokenParameters() as $parameter) {
            $fieldName = $parameter->getName();
            $this->columns[$fieldName] = $options[$fieldName];
        }
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
            $idCol = $connection->quoteIdentifier($this->columns['tokenId']);
            $sql = "SELECT * FROM {$table} WHERE {$idCol} = :id";

            $stmt = $connection->prepare($sql);
            $stmt->bindValue('tokenId', $tokenId);
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
            $idCol = $connection->quoteIdentifier($this->columns['tokenId']);
            $sql = "SELECT * FROM {$table} WHERE {$idCol} = :id";

            $stmt = $connection->prepare($sql);
            $stmt->bindValue('tokenId', $tokenId);
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
            $idCol = $connection->quoteIdentifier($this->columns['tokenId']);
            $sql = "DELETE FROM {$table} WHERE {$idCol} = :id";

            $stmt = $connection->prepare($sql);
            $stmt->bindValue('tokenId', $tokenId);
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
            $idCol = $connection->quoteIdentifier($this->columns['tokenId']);
            $valueCol = $connection->quoteIdentifier($this->columns['value']);

            $sql = $this->hasToken($tokenId) ?
                    "UPDATE {$table} SET {$valueCol} = :value WHERE {$idCol} = :id" :
                    "INSERT INTO {$table} ({$idCol}, {$valueCol}) VALUES (:id, :value)";

            $stmt = $connection->prepare($sql);
            $stmt->bindValue('tokenId', $tokenId);
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

    /**
     * Whether or not the provided options are ok
     * 
     * @param Doctrine\DBAL\Connection $connection
     * @param array $options
     * @throws DelOlmo\Token\Exception\DbMissingTableExecption
     * @throws DelOlmo\Token\Exception\DbTableNotFoundException
     * @throws DelOlmo\Token\Exception\DbMissingColumnException
     * @throws DelOlmo\Token\Exception\DbColumnNotFoundException
     */
    protected static function checkStorageOptions(Connection $connection, array $options): bool
    {
        // Check that the table parameter has been passed
        if (!isset($options['table'])) {
            $str = "The name of the table holding the tokens is missing";
            throw new DbMissingTableException($str);
        }

        // Check that the table exists
        if (!self::tableExists($connection, $options['table'])) {
            $str = "'%s' does not appear to be a valid table name";
            $message = sprintf($str, $options['table']);
            throw new DbTableNotFoundException($message);
        }

        // Loop through all the required parameters of the setToken method
        foreach (self::getTokenParameters() as $parameter) {

            // The required field name of the setToken method
            $fieldName = $parameter->getName();

            // Check that all parameters have been passed
            if (!isset($options['columns'][$fieldName])) {
                $str = "The column '%s' field is missing";
                $message = sprintf($str, $parameter->getName());
                throw new DbMissingColumnException($message);
            }

            // Check that the given fields exist
            if (!self::fieldExists($connection, $options['table'], $options['columns'][$fieldName])) {
                $str = "'%s' does not appear to be a valid column name";
                $message = sprintf($str, $options['columns'][$fieldName], $options['table']);
                throw new DbColumnNotFoundException($message);
            }
        }

        return true;
    }

    /**
     * Returns an array with all the required parameters 
     * 
     * @return array
     */
    protected static function getTokenParameters(): array
    {

        if (self::$parameters === null) {
            $interface = array_keys(class_implements(__CLASS__));
            $reflection = new \ReflectionClass($interface[0]);
            $setTokenMethod = $reflection->getMethod('setToken');
            self::$parameters = $setTokenMethod->getParameters();
        }

        return self::$parameters;
    }

    /**
     * Whether or not a table exists for the given database connection
     * 
     * @param Connection $connection
     * @param string $table
     * @return bool
     */
    protected static function tableExists(Connection $connection, string $table): bool
    {
        $schema = $connection->getSchemaManager();
        return $schema->tablesExist([$table]);
    }

    /**
     * Whether or not a field exists for the given database connection and 
     * table name.
     * 
     * @param Connection $connection
     * @param string $table
     * @param string $column
     * @return bool
     */
    protected static function fieldExists(Connection $connection, string $table, string $field): bool
    {
        if (!self::tableExist($connection, $table)) {
            return false;
        }

        $schema = $connection->getSchemaManager();

        $columns = $schema->listTableColumns($table);

        $columnsList = [];
        foreach ($columns as $column) {
            $columnsList[] = $column->getName();
        }

        return in_array($field, $columnsList);
    }

}
