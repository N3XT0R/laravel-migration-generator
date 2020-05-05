<?php


namespace Tests\Integration\Service\Generator;


use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompilerInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use Tests\TestCase;

class MigrationCompilerTest extends TestCase
{
    protected $compiler;

    public function setUp(): void
    {
        parent::setUp();
        $this->compiler = $this->app->make(MigrationCompilerInterface::class);
    }


    public function testGenerateByResultWorks(): void
    {
        $fieldEntity = new FieldEntity();
        $fieldEntity->setColumnName('test');
        $fieldEntity->setType('bigInteger');
        $fieldEntity->addOption('unsigned', true);
        $fieldEntity->addArgument('autoIncrement', true);
        $result = new ResultEntity();
        $result->setTableName('test_table');
        $result->setResults(
            [
                'test_table' => [
                    'table' => [$fieldEntity],
                ],
            ]
        );
        $this->compiler->generateByResult($result);
        $result = $this->compiler->getRenderedTemplate();
        $this->assertStringEqualsFile(__DIR__ . '/expectedResults/migrationCompilerResult.txt', $result);
    }
}