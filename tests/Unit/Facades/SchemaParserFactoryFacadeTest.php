<?php

namespace Facades;

use N3XT0R\MigrationGenerator\Facades\SchemaParserFactoryFacade;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserFactory;
use Tests\TestCase;

class SchemaParserFactoryFacadeTest extends TestCase
{
    public function testFaceAccessorReturnsSchemaParserFactory(): void
    {
        $result = SchemaParserFactoryFacade::getFacadeRoot();
        self::assertInstanceOf(SchemaParserFactory::class, $result);
    }
}