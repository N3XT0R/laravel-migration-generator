<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;

use Doctrine\DBAL\Schema\Column;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;

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
        return [
            'fields' => $this->generateFields($table, $columns),
            'indexes' => [],
            'foreignKeys' => [],
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
                $defaultValue = $column->getDefault();
                $notNullable = $column->getNotnull();
                $type = $this->convertTypeToBluePrintType($column->getType()->getName());
                $comment = $column->getComment();

                $options = [
                    'nullable' => !$notNullable,
                ];


                switch ($type) {
                    case 'tinyInteger':
                    case 'integer':
                    case 'smallInteger':
                    case 'bigInteger':
                        $options['unsigned'] = $column->getUnsigned();
                        $options['autoIncrement'] = $column->getAutoincrement();

                        break;

                    case 'dateTime':
                        if ('CURRENT_TIMESTAMP' === $defaultValue) {
                            $defaultValue = 'DB::raw(\'CURRENT_TIMESTAMP\')';
                        }
                        
                        break;

                    case 'double':
                    case 'float':
                    case 'decimal':
                        $column->getPrecision();
                        $column->getScale();
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
                $result[] = $fieldEntity;
            }
        }

        return $result;
    }

}