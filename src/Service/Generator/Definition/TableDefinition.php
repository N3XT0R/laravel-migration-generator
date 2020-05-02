<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\AbstractAsset;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\IndexEntity;

class TableDefinition extends AbstractDefinition
{

    protected $fieldTypeMap = [
        'tinyint' => 'tinyInteger',
        'smallint' => 'smallInteger',
        'bigint' => 'bigInteger',
        'datetime' => 'dateTime',
        'blob' => 'binary',
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
        $table = $this->getAttributeByName('table');
        $table = 'da_attribute';

        $schema = $this->getSchema();
        $indexes = $schema->listTableIndexes($table);
        $foreignKeys = $schema->listTableForeignKeys($table);
        $columns = $schema->listTableColumns($table);

        $fields = $this->generateFields($table, $columns);
        $combinedIndexes = $this->generateIndexes($fields, $indexes);


        return [
            'fields' => $fields,
            'combinedIndexes' => $combinedIndexes,
        ];
    }


    protected function generateFields(string $table, array $columns): array
    {
        $result = [];

        if (0 !== count($columns)) {
            foreach ($columns as $column) {
                if ($column instanceof Column === false) {
                    continue;
                }

                $fieldEntity = new FieldEntity();
                $fieldEntity->setTable($table);
                $fieldEntity->setColumnName($column->getName());
                $fieldEntity->setComment((string)$column->getComment());
                $defaultValue = $column->getDefault();
                $notNullable = $column->getNotnull();
                $type = $this->convertTypeToBluePrintType($column->getType()->getName());

                $arguments = [];
                $options = [
                    'nullable' => !$notNullable,
                ];


                switch ($type) {
                    case 'tinyInteger':
                    case 'integer':
                    case 'smallInteger':
                    case 'bigInteger':
                        if (true === $column->getAutoincrement()) {
                            $type = $this->convertTypeToBluePrintType($type);
                        }
                        $arguments['unsigned'] = $column->getUnsigned();
                        $arguments['autoIncrement'] = $column->getAutoincrement();

                        break;

                    case 'dateTime':
                        if ('CURRENT_TIMESTAMP' === $defaultValue) {
                            $defaultValue = 'DB::raw(\'CURRENT_TIMESTAMP\')';
                        }

                        break;

                    case 'double':
                    case 'float':
                    case 'decimal':
                        $arguments['unsigned'] = $column->getUnsigned();
                        $arguments['total'] = $column->getPrecision();
                        $arguments['places'] = $column->getScale();
                        break;

                    default:
                        if ('string' === $type && true === $column->getFixed()) {
                            $type = 'char';
                            $fieldEntity->setLength($column->getLength());
                        }
                        break;
                }

                $fieldEntity->setType($type);
                $options['default'] = $defaultValue;
                $fieldEntity->setOptions($options);
                $fieldEntity->setArguments($arguments);
                $result[$fieldEntity->getColumnName()] = $fieldEntity;
            }
        }

        return $result;
    }

    protected function generateIndexes(array $fields, array $indexes): array
    {
        $combinedIndexes = [];
        foreach ($indexes as $index) {
            if ($index instanceof Index === false) {
                continue;
            }

            $fieldEntity = null;
            $columns = $index->getColumns();
            $isCombinedIndex = count($columns) > 1;

            switch (true) {
                case true === $index->isUnique():
                    $type = 'unique';
                    break;

                case true === $index->isPrimary():
                    $type = 'primary';
                    break;

                default:
                    $type = 'index';
                    break;
            }

            $indexEntity = new IndexEntity();
            $indexEntity->setType($type);
            $indexEntity->setName($index->getName());

            if (false === $isCombinedIndex) {
                $column = current($columns);
                /**
                 * @var FieldEntity $fieldEntity
                 */
                $fieldEntity = $fields[$column];
                $fieldEntity->addIndex($indexEntity);
            } else {
                $indexEntity->setColumns($columns);
                $combinedIndexes[] = $indexEntity;
            }
        }

        return $combinedIndexes;
    }
}