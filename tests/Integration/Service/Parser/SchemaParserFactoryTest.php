<?php

namespace Service\Parser;

use Illuminate\Database\Connection;
use N3XT0R\MigrationGenerator\Service\Parser\Drivers\MSSQLSchemaParser;
use N3XT0R\MigrationGenerator\Service\Parser\Drivers\MySQL8SchemaParser;
use N3XT0R\MigrationGenerator\Service\Parser\Drivers\MySQLSchemaParser;
use N3XT0R\MigrationGenerator\Service\Parser\Drivers\PostgresSchemaParser;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SchemaParserFactoryTest extends TestCase
{
    public static function connectionDriverDataProvider(): array
    {
        return [
            ['mysql', MySQLSchemaParser::class],
            ['mysql8', MySQL8SchemaParser::class],
            ['pgsql', PostgresSchemaParser::class],
            ['sqlsrv', MSSQLSchemaParser::class],
        ];
    }

    #[DataProvider('connectionDriverDataProvider')]
    public function testFactoryWorks(string $dbType, string $expectedType): void
    {
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDriverName'])->getMock();
        $connection->method('getDriverName')->willReturn($dbType);

        $factory = new SchemaParserFactory();
        self::assertInstanceOf($expectedType, $factory->create($connection));
    }

    public function testFactoryThrowsException(): void
    {
        $dbType = uniqid('dbType', true);
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDriverName'])->getMock();
        $connection->method('getDriverName')->willReturn($dbType);
        $this->expectException(\InvalidArgumentException::class);
        (new SchemaParserFactory())->create($connection);
    }
}