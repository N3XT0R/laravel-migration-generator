<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity;

class FieldEntity
{
    protected $table = '';
    protected $columnName = '';
    protected $type = '';
    protected $comment = '';
    protected $arguments = [];
    protected $length = 0;
    protected $options = [];
    protected $indexes = [];
    protected $foreignKeys = [];

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->columnName;
    }

    /**
     * @param string $columnName
     */
    public function setColumnName(string $columnName): void
    {
        $this->columnName = $columnName;
    }

    /**
     * @return string
     */
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
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param int $length
     */
    public function setLength(int $length): void
    {
        $this->length = $length;
    }


    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getForeignKeys(): array
    {
        return $this->foreignKeys;
    }

    /**
     * @param array $foreignKeys
     */
    public function setForeignKeys(array $foreignKeys): void
    {
        $this->foreignKeys = $foreignKeys;
    }

    public function addForeignKey(ForeignKeyEntity $entity): void
    {
        $this->foreignKeys[] = $entity;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @return array
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * @param array $indexes
     */
    public function setIndexes(array $indexes): void
    {
        $this->indexes = $indexes;
    }

    public function addIndex(IndexEntity $indexEntity): void
    {
        $this->indexes[] = $indexEntity;
    }

    public function hasIndexes(): bool
    {
        return 0 !== count($this->getIndexes());
    }
}