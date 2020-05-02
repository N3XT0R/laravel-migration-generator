<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
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
}