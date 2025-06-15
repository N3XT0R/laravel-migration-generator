<?php


namespace Tests\Integration\Service\Parser;


use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserInterface;
use Tests\DbTestCase;

class SchemaParserTest extends DbTestCase
{
    protected SchemaParserInterface $parser;

    public function setUp(): void
    {
        parent::setUp();
        $this->parser = $this->app->make(SchemaParserInterface::class);
    }

    public function testGetTablesFromSchemaWorks(): void
    {
        $tables = $this->parser->getTablesFromSchema('testing');
        sort($tables); // optional: um Reihenfolgevergleich robust zu machen

        $laravelVersion = \Illuminate\Foundation\Application::VERSION;

        $expectedTables = match (true) {
            str_starts_with($laravelVersion, '10.') => [
                'abc',
                'cache',
                'cache_locks',
                'failed_jobs',
                'fields_test',
                'foreign_table',
                'job_batches',
                'jobs',
                'password_reset_tokens',
                'sessions',
                'users',
            ],
            str_starts_with($laravelVersion, '11.') => [
                'abc',
                'cache',
                'cache_locks',
                'failed_jobs',
                'fields_test',
                'foreign_table',
                'job_batches',
                'jobs',
                'password_reset_tokens',
                'personal_access_tokens', // neu in Laravel 11 Setup
                'sessions',
                'users',
            ],
            default => throw new \RuntimeException("Laravel version $laravelVersion not supported in test"),
        };

        sort($expectedTables); // für sicheren Vergleich

        self::assertSame($expectedTables, $tables);
    }

    public function testGetSortedTablesFromSchema(): void
    {
        $tables = $this->parser->getSortedTablesFromSchema('testing');
        $laravelVersion = \Illuminate\Foundation\Application::VERSION;

        $expectedTables = match (true) {
            str_starts_with($laravelVersion, '10.') => [
                'cache',
                'cache_locks',
                'failed_jobs',
                'fields_test',
                'foreign_table',
                'job_batches',
                'jobs',
                'password_reset_tokens',
                'sessions',
                'users',
                'abc',
            ],
            str_starts_with($laravelVersion, '11.') => [
                'cache',
                'cache_locks',
                'failed_jobs',
                'fields_test',
                'foreign_table',
                'job_batches',
                'jobs',
                'password_reset_tokens',
                'personal_access_tokens',
                'sessions',
                'users',
                'abc',
            ],
            default => throw new \RuntimeException("Laravel version $laravelVersion not supported"),
        };

        self::assertCount(count($expectedTables), $tables);
        self::assertSame($expectedTables, $tables);
    }
}