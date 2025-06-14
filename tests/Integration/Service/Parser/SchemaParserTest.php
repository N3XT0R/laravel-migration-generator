<?php


namespace Tests\Integration\Service\Parser;


use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserInterface;
use Tests\DbTestCase;

class SchemaParserTest extends DbTestCase
{
    protected $parser;

    public function setUp(): void
    {
        parent::setUp();
        $this->parser = $this->app->make(SchemaParserInterface::class);
    }

    public function testGetTablesFromSchemaWorks(): void
    {
        $tables = $this->parser->getTablesFromSchema('testing');
        self::assertCount(11, $tables);
        self::assertSame(
            [
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
            $tables
        );
    }

    public function testGetSortedTablesFromSchema(): void
    {
        $tables = $this->parser->getSortedTablesFromSchema('testing');
        self::assertCount(11, $tables);
        self::assertSame(
            [
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
            $tables
        );
    }
}