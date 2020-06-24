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
        $migrationPath = $this->compiler->getMigrationFiles()[0];
        $this->assertFileExists($path . $migrationPath);
        file_put_contents($this->resourceFolder . '/ExpectedResults/renderedClass.txt',
            file_get_contents($path . $migrationPath));
        $this->assertFileEquals($this->resourceFolder . '/ExpectedResults/renderedClass.txt', $path . $migrationPath);
    }
}