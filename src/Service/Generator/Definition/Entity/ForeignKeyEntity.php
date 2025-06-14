<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity;


class ForeignKeyEntity
{
    protected string $name = '';
    protected string $localTable = '';
    protected string $localColumn = '';
    protected string $referencedTable = '';
    protected string $referencedColumn = '';
    protected string $onDelete = '';
    protected string $onUpdate = '';

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLocalTable(): string
    {
        return $this->localTable;
    }

    /**
     * @param string $localTable
     */
    public function setLocalTable(string $localTable): void
    {
        $this->localTable = $localTable;
    }

    /**
     * @return string
     */
    public function getLocalColumn(): string
    {
        return $this->localColumn;
    }

    /**
     * @param string $localColumn
     */
    public function setLocalColumn(string $localColumn): void
    {
        $this->localColumn = $localColumn;
    }
    

    /**
     * @return string
     */
    public function getReferencedTable(): string
    {
        return $this->referencedTable;
    }

    /**
     * @param string $referencedTable
     */
    public function setReferencedTable(string $referencedTable): void
    {
        $this->referencedTable = $referencedTable;
    }

    /**
     * @return string
     */
    public function getReferencedColumn(): string
    {
        return $this->referencedColumn;
    }

    /**
     * @param string $referencedColumn
     */
    public function setReferencedColumn(string $referencedColumn): void
    {
        $this->referencedColumn = $referencedColumn;
    }

    /**
     * @return string
     */
    public function getOnDelete(): string
    {
        return $this->onDelete;
    }

    /**
     * @param string $onDelete
     */
    public function setOnDelete(string $onDelete): void
    {
        $this->onDelete = $onDelete;
    }

    /**
     * @return string
     */
    public function getOnUpdate(): string
    {
        return $this->onUpdate;
    }

    /**
     * @param string $onUpdate
     */
    public function setOnUpdate(string $onUpdate): void
    {
        $this->onUpdate = $onUpdate;
    }

}