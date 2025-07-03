<?php

namespace Generator\Normalization\Processors;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\PrimaryKeyEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Context\NormalizationContext;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors\PivotProcessor;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors\ProcessorInterface;
use Tests\TestCase;

class PivotProcessorTest extends TestCase
{
    protected ProcessorInterface $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = new PivotProcessor();
    }

    public function test_pivot_processor_replaces_primary_with_id_and_adds_unique_index(): void
    {
        $pk = new PrimaryKeyEntity();
        $pk->setColumns(['role_id', 'user_id']);

        $roleId = new FieldEntity();
        $roleId->setType('bigInteger');
        $roleId->setColumnName('role_id');
        $roleId->setOptions(['nullable' => false, 'unsigned' => true, 'default' => null]);

        $userId = new FieldEntity();
        $userId->setType('bigInteger');
        $userId->setColumnName('user_id');
        $userId->setOptions(['nullable' => false, 'unsigned' => true, 'default' => null]);

        $result = new ResultEntity();
        $result->setTableName('role_customer');
        $result->setResults([
            'role_customer' => [
                'table' => [
                    'role_id' => $roleId,
                    'user_id' => $userId,
                ],
                'primaryKey' => ['PRIMARY' => $pk],
            ],
        ]);

        $context = new NormalizationContext($result);
        $table = (new PivotProcessor())->process($context)->getResultByTable('role_customer');

        $this->assertArrayHasKey('id', $table['table']);
        $this->assertArrayNotHasKey('primaryKey', $table);
        $this->assertNotEmpty($table['index']);

        $unique = array_filter($table['index'], fn($i) => $i->getType() === 'unique');
        $this->assertSame(['role_id', 'user_id'], array_values($unique)[0]->getColumns());
    }


}