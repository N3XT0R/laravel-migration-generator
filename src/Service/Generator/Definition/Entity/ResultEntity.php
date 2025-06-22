<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity;


class ResultEntity
{
    protected array $results = [];
    protected string $tableName = '';

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

    public function hasResultForTable(string $tableName): bool
    {
        $results = $this->getResults();
        return array_key_exists($tableName, $results);
    }

    public function getResultByTable(string $tableName): array
    {
        $result = [];
        if ($this->hasResultForTable($tableName)) {
            $result = $this->getResults()[$tableName];
        }

        return $result;
    }

    public function hasResultForTableNameAndKey(string $tableName, string $key): bool
    {
        return $this->hasResultForTable($tableName) && array_key_exists($key, $this->getResults()[$tableName]);
    }
}