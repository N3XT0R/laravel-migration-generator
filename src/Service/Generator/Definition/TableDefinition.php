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
                    $fieldEntity = new FieldEntity();
                    $fieldEntity->setTable($table);
                    $fieldEntity->setColumnName($column->getName());
                    $defaultValue = $column->getDefault();
                    $notNullable = $column->getNotnull();
                    $type = $this->convertTypeToBluePrintType($column->getType()->getName());

                    $arguments = [];
                    $options = [
                        'nullable' => !$notNullable,
                        'comment' => null,
                    ];

                    if (null !== $column->getComment()) {
                        $options['comment'] = $column->getComment();
                    }


                    switch ($type) {
                        case 'tinyInteger':
                        case 'integer':
                        case 'smallInteger':
                        case 'bigInteger':
                            $options['unsigned'] = $column->getUnsigned();
                            $autoIncrement = $column->getAutoincrement();
                            if (true === $autoIncrement) {
                                $arguments['autoIncrement'] = $autoIncrement;
                            }

                            break;

                        case 'dateTime':
                            $type = 'timestamp';
                            break;

                        case 'double':
                        case 'decimal':
                            $arguments['total'] = $column->getPrecision();
                            $arguments['places'] = $column->getScale();
                            $unsigned = $column->getUnsigned();

                            if ('float' !== $type && $unsigned) {
                                $type = 'unsigned' . ucfirst($type);
                            } else {
                                $options['unsigned'] = $unsigned;
                            }
                            break;

                        default:
                            if ('string' === $type && true === $column->getFixed()) {
                                $type = 'char';
                                $arguments['length'] = $column->getLength();
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
        }

        return $result;
    }
}