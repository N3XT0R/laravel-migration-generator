<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;

use Doctrine\DBAL\Schema\Column;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;

class TableDefinition extends AbstractDefinition
{

    protected array $fieldTypeMap = [
        'tinyint' => 'tinyInteger',
        'smallint' => 'smallInteger',
        'bigint' => 'bigInteger',
        'datetime' => 'dateTime',
        'blob' => 'binary',
        'float' => 'double',
    ];


    public function setFieldTypeMap(array $fieldTypeMap): void
    {
        $this->fieldTypeMap = $fieldTypeMap;
    }

    public function getFieldTypeMap(): array
    {
        return $this->fieldTypeMap;
    }

    public function convertTypeToBluePrintType(string $type): string
    {
        $value = $type;
        $map = $this->getFieldTypeMap();

        if (array_key_exists($type, $map)) {
            $value = $map[$type];
        }

        return $value;
    }

    protected function generateData(): array
    {
        $table = $this->getAttributeByName('tableName');
        $schema = $this->getSchema();
        $columns = $schema->listTableColumns($table);

        return $this->generateFields($table, $columns);
    }


    protected function generateFields(string $table, array $columns): array
    {
        $result = [];
        if (0 !== count($columns)) {
            foreach ($columns as $column) {
                if ($column instanceof Column) {
                    $fieldEntity = $this->convertColumnToFieldEntity($table, $column);
                    $result[$fieldEntity->getColumnName()] = $fieldEntity;
                }
            }
        }

        return $result;
    }

    protected function convertColumnToFieldEntity(string $table, Column $column): FieldEntity
    {
        $fieldEntity = new FieldEntity();
        $fieldEntity->setTable($table);
        $fieldEntity->setColumnName($column->getName());
        $fieldEntity->setType($this->convertTypeToBluePrintType($column->getType()->getName()));
        $fieldEntity->setOptions($this->buildOptions($column));
        $this->dispatchTypePreparation($fieldEntity, $column);

        return $fieldEntity;
    }

    protected function buildOptions(Column $column): array
    {
        $options = [
            'default' => $column->getDefault(),
            'nullable' => !$column->getNotnull(),
        ];

        if (null !== $column->getComment()) {
            $options['comment'] = $column->getComment();
        }

        return $options;
    }

    protected function dispatchTypePreparation(FieldEntity $fieldEntity, Column $column): void
    {
        switch ($fieldEntity->getType()) {
            case 'tinyInteger':
            case 'integer':
            case 'smallInteger':
            case 'bigInteger':
                $this->prepareInteger($fieldEntity, $column);
                break;
            case 'dateTime':
                $this->prepareDateTime($fieldEntity);
                break;
            case 'double':
            case 'decimal':
                $this->prepareFloatingField($fieldEntity, $column);
                break;
            default:
                $this->prepareMixedTypes($fieldEntity, $column);
                break;
        }
    }

    private function prepareInteger(FieldEntity $fieldEntity, Column $column): void
    {
        $unsigned = $column->getUnsigned();
        $autoIncrement = $column->getAutoincrement();
        if (true === $autoIncrement) {
            $fieldEntity->addArgument('autoIncrement', $autoIncrement);
        }

        if (true === $unsigned) {
            $fieldEntity->addOption('unsigned', $unsigned);
        }
    }

    private function prepareDateTime(FieldEntity $fieldEntity): void
    {
        $fieldEntity->setType('timestamp');
    }

    private function prepareFloatingField(FieldEntity $fieldEntity, Column $column): void
    {
        $fieldEntity->addArgument('total', $column->getPrecision());
        $fieldEntity->addArgument('places', $column->getScale());
        $unsigned = $column->getUnsigned();
        $type = $fieldEntity->getType();

        if (true === $unsigned && 'float' !== $type) {
            $fieldEntity->setType('unsigned' . ucfirst($type));
        } elseif (true === $unsigned) {
            $fieldEntity->addOption('unsigned', $unsigned);
        }
    }

    private function prepareMixedTypes(FieldEntity $fieldEntity, Column $column): void
    {
        $type = $fieldEntity->getType();

        if ('string' === $type && true === $column->getFixed()) {
            $fieldEntity->setType('char');
            $fieldEntity->addArgument('length', $column->getLength());
        }
    }

}