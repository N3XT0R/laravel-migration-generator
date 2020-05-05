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
        $this->assertCount(3, $tables);
        $this->assertEquals(
            [
                'abc',
                'fields_test',
                'foreign_table',
            ],
            $tables
        );
    }

    public function testGetSortedTablesFromSchema(): void
    {
        $tables = $this->parser->getSortedTablesFromSchema('testing');
        $this->assertCount(3, $tables);
        $this->assertEquals(
            [
                'fields_test',
                'foreign_table',
                'abc',
            ],
            $tables
        );
    }
}