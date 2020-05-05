<?php


namespace Tests\Integration\Service\Generator\Compiler;


use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompilerInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use Tests\Resources\Classes\CustomMigration;
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
        $this->assertStringEqualsFile($this->resourceFolder . '/ExpectedResults/migrationCompilerResult.txt', $result);
    }

    public function testGenerateByResultWorksWithCustomMigration(): void
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
        $this->compiler->generateByResult($result, CustomMigration::class);
        $result = $this->compiler->getRenderedTemplate();
        $this->assertStringEqualsFile(
            $this->resourceFolder . '/ExpectedResults/migrationCompilerResult_custom.txt',
            $result
        );
    }

    public function testWriteToDiskWorks(): void
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

        $path = $this->resourceFolder . '/ExpectedResults/';
        $this->assertTrue($this->compiler->writeToDisk('create_test_table', $path));
        $this->assertFileExists($path . $this->compiler->getMigrationFiles()[0]);
    }
}