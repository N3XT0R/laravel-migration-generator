<?php


namespace Tests\Integration\Service\Generator\Definition;


use Doctrine\DBAL\DriverManager;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\TableDefinition;
use PHPUnit\Framework\Attributes\Depends;
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
        $dbConfig = $dbManager->connection()->getConfig();
        $connectionParams = [
            'dbname' => $dbConfig['database'],
            'user' => $dbConfig['username'],
            'password' => $dbConfig['password'],
            'host' => $dbConfig['host'],
            'driver' => 'pdo_mysql',
        ];
        $doctrine = DriverManager::getConnection($connectionParams);
        $schema = $doctrine->createSchemaManager();

        $definition = new TableDefinition();
        $definition->setSchema($schema);
        $definition->addAttribute('tableName', 'fields_test');

        $this->definition = $definition;
    }

    public function testGenerateResultShouldWork(): array
    {
        $this->definition->generate();
        $result = $this->definition->getResult();
        $this->assertCount(14, $result);
        $this->assertContainsOnlyInstancesOf(FieldEntity::class, $result);
        foreach (array_keys($result) as $key) {
            $this->assertIsString($key);
        }

        return $result;
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
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
                'default' => null,
                'nullable' => false,
                'unsigned' => true,
            ],
            $bigInteger->getOptions()
        );
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
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
                'default' => null,
                'nullable' => true,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
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
                'default' => null,
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
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
                'default' => '1',
                'nullable' => false,
                'comment' => 'my tiny int',
            ],
            $field->getOptions()
        );
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
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
                'default' => 'CURRENT_TIMESTAMP',
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
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
                'default' => null,
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
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
                'total' => 10,
                'places' => 0,
            ],
            $field->getArguments()
        );
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'default' => null,
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
    public function testFloatIsCorrect(array $result): void
    {
        /** @var FieldEntity $field */
        $field = $result['float_value'];

        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('float_value', $field->getColumnName());
        $this->assertEquals('double', $field->getType());

        if (str_starts_with(Application::VERSION, '10.')) {
            $this->assertSame(
                [
                    'total' => 6,
                    'places' => 2,
                ],
                $field->getArguments()
            );
        } else {
            $this->assertSame(
                [
                    'total' => 10,
                    'places' => 0,
                ],
                $field->getArguments()
            );
        }

        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'default' => null,
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
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
                'default' => null,
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
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
                'default' => null,
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
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
                'default' => null,
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
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
                'default' => null,
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
    public function testJsonIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['json'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('json', $field->getColumnName());
        $this->assertEquals('json', $field->getType());
        $this->assertCount(0, $field->getArguments());
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'default' => null,
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
    public function testJsonBIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['jsonb'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('jsonb', $field->getColumnName());
        $this->assertEquals('json', $field->getType());
        $this->assertCount(0, $field->getArguments());
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'default' => null,
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }
}