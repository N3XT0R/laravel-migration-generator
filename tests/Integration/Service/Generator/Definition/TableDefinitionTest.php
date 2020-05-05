<?php


namespace Tests\Integration\Service\Generator\Definition;


use Illuminate\Database\DatabaseManager;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\TableDefinition;
use Tests\DbTestCase;

class TableDefinitionTest extends DbTestCase
{
    protected $definition;

    public function setUp(): void
    {
        parent::setUp();
        /**
         * @var DatabaseManager $dbManager
         */
        $dbManager = $this->app->get('db');
        $doctrine = $dbManager->connection()->getDoctrineConnection();
        $schema = $doctrine->getSchemaManager();

        $definition = new TableDefinition();
        $definition->setSchema($schema);
        $definition->addAttribute('tableName', 'fields_test');

        $this->definition = $definition;
    }

    public function testGenerateResultShouldWork(): array
    {
        $this->definition->generate();
        $result = $this->definition->getResult();
        $this->assertCount(12, $result);
        $this->assertContainsOnlyInstancesOf(FieldEntity::class, $result);
        $this->assertContainsOnly('string', array_keys($result), true);

        return $result;
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testBigIntegerIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $bigInteger
         */
        $bigInteger = $result['id'];
        $this->assertEquals('fields_test', $bigInteger->getTable());
        $this->assertEquals('id', $bigInteger->getColumnName());
        $this->assertEquals('bigInteger', $bigInteger->getType());
        $this->assertCount(1, $bigInteger->getArguments());
        $this->assertSame(
            [
                'autoIncrement' => true,
            ],
            $bigInteger->getArguments()
        );
        $this->assertCount(3, $bigInteger->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'unsigned' => true,
                'default' => null,
            ],
            $bigInteger->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testSmallIntegerIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['small_int'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('small_int', $field->getColumnName());
        $this->assertEquals('smallInteger', $field->getType());
        $this->assertCount(0, $field->getArguments());
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => true,
                'default' => null,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testMediumIntegerIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['medium_int'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('medium_int', $field->getColumnName());
        $this->assertEquals('integer', $field->getType());
        $this->assertCount(0, $field->getArguments());
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'default' => null,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testTinyIntegerIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['tiny_int'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('tiny_int', $field->getColumnName());
        $this->assertEquals('boolean', $field->getType());
        $this->assertCount(0, $field->getArguments());
        $this->assertCount(3, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'comment' => 'my tiny int',
                'default' => '1',
            ],
            $field->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testTimestampIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['created_at'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('created_at', $field->getColumnName());
        $this->assertEquals('timestamp', $field->getType());
        $this->assertCount(0, $field->getArguments());
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ],
            $field->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testDateTimeIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['any_date'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('any_date', $field->getColumnName());
        $this->assertEquals('timestamp', $field->getType());
        $this->assertCount(0, $field->getArguments());
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'default' => null,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testDoubleIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['double_value'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('double_value', $field->getColumnName());
        $this->assertEquals('double', $field->getType());
        $this->assertCount(2, $field->getArguments());
        $this->assertSame(
            [
                'total' => 4,
                'places' => 2,
            ],
            $field->getArguments()
        );
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'default' => null,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testFloatIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['float_value'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('float_value', $field->getColumnName());
        $this->assertEquals('double', $field->getType());
        $this->assertCount(2, $field->getArguments());
        $this->assertSame(
            [
                'total' => 6,
                'places' => 3,
            ],
            $field->getArguments()
        );
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'default' => null,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testDecimalIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['decimal_value'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('decimal_value', $field->getColumnName());
        $this->assertEquals('unsignedDecimal', $field->getType());
        $this->assertCount(2, $field->getArguments());
        $this->assertSame(
            [
                'total' => 2,
                'places' => 1,
            ],
            $field->getArguments()
        );
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'default' => null,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testStringIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['string'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('string', $field->getColumnName());
        $this->assertEquals('string', $field->getType());
        $this->assertCount(0, $field->getArguments());
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'default' => null,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testCharIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['char'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('char', $field->getColumnName());
        $this->assertEquals('char', $field->getType());
        $this->assertCount(1, $field->getArguments());
        $this->assertSame(
            [
                'length' => 5
            ],
            $field->getArguments()
        );
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'default' => null,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testBooleanIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['boolean'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('boolean', $field->getColumnName());
        $this->assertEquals('boolean', $field->getType());
        $this->assertCount(0, $field->getArguments());
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'default' => null,
            ],
            $field->getOptions()
        );
    }
}