<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity;


class IndexEntity extends AbstractIndexEntity
{
    protected string $type = '';
    protected array $columns = [];

    protected string $indexType = 'index';


    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

}