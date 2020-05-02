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

    public function hasResult(string $key): bool
    {
        return array_key_exists($key, $this->results);
    }
}