<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;

use  Doctrine\DBAL\Types;

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
        $result = [];
        $table = $this->getAttributeByName('table');
        $table = 't_case';
        $schema = $this->getSchema();
        $columns = $schema->listTableColumns($table);
        if (0 !== count($columns)) {
            foreach ($columns as $column) {
                $columnName = $column->getName();
                $defaultValue = $column->getDefault();
                $notNullable = $column->getNotnull();
                $length = $column->getLength();
                $type = $this->convertTypeToBluePrintType($column->getType()->getName());
                var_dump($type);
                $comment = $column->getComment();
                $isUnsigned = $column->getUnsigned();


                switch ($type) {
                    case 'tinyInteger':
                    case 'integer':
                    case 'smallInteger':
                    case 'bigInteger':

                        break;
                }

                $result['field'][$columnName] = [

                ];
            }
        }

        return $result;
    }

}