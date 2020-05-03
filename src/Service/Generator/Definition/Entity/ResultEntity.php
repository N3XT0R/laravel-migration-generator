<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity;


use phpDocumentor\Reflection\Types\Boolean;

class ResultEntity
{
    protected $results = [];
    protected $tableName = '';

    public function setResults(array $results): void
    {
        $this->results = $results;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     */
    public function setTableName(string $tableName): void
    {
        $this->tableName = $tableName;
    }


    public function getResultByTableNameAndKey(string $tableName, string $key): array
    {
        $result = [];
        if ($this->hasResultForTableNameAndKey($tableName, $key)) {
            $result = $this->getResults()[$tableName][$key];
        }

        return $result;
    }

    public function setResultByKey(string $key, array $data): void
    {
        $this->results[$key] = $data;
    }

    public function hasResultForTable(string $tableName): bool
    {
        return array_key_exists($tableName, $this->results);
    }

    public function hasResultForTableNameAndKey(string $tableName, string $key): bool
    {
        return $this->hasResultForTable($tableName) && array_key_exists($key, $this->getResults()[$tableName]);
    }
}