<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity;


class ResultEntity
{
    protected $results = [];

    public function setResults(array $results): void
    {
        $this->results = $results;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function getResultByKey(string $key): array
    {
        $result = [];
        if ($this->hasResult($key)) {
            $result = $this->getResults()[$key];
        }

        return $result;
    }

    public function setResultByKey(string $key, array $data): void
    {
        $this->results[$key] = $data;
    }

    public function hasResult(string $key): bool
    {
        return array_key_exists($key, $this->results);
    }
}