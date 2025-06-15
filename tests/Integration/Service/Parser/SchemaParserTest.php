<?php


namespace Tests\Integration\Service\Parser;


use Illuminate\Foundation\Application;
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

        $laravelVersion = Application::VERSION;

        $expectedTables = match (true) {
            str_starts_with($laravelVersion, '10.') => [
                'abc',
                'failed_jobs',
                'fields_test',
                'foreign_table',
                'password_reset_tokens',
                'users',
            ],
            default => [ // Laravel 11+ (Standard)
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
        };

        sort($expectedTables); // für sicheren Vergleich

        self::assertSame($expectedTables, $tables);
    }

    public function testGetSortedTablesFromSchema(): void
    {
        $tables = $this->parser->getSortedTablesFromSchema('testing');
        $laravelVersion = Application::VERSION;

        $expectedTables = match (true) {
            str_starts_with($laravelVersion, '10.') => [
                'failed_jobs',
                'fields_test',
                'foreign_table',
                'password_reset_tokens',
                'users',
                'abc',
            ],
            default => [ // Laravel 11+ (Standard)
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
        };

        self::assertCount(count($expectedTables), $tables);
        self::assertSame($expectedTables, $tables);
    }
}