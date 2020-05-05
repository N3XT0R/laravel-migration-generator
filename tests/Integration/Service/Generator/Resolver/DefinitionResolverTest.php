<?php


namespace Tests\Integration\Service\Generator\Resolver;


use Illuminate\Database\DatabaseManager;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolverInterface;
use Tests\TestCase;

class DefinitionResolverTest extends TestCase
{
    protected $resolver;

    public function setUp(): void
    {
        parent::setUp();
        /**
         * @var DatabaseManager $dbManager
         */
        $dbManager = $this->app->get('db');
        $doctrine = $dbManager->connection()->getDoctrineConnection();
        $this->resolver = $this->app->make(DefinitionResolverInterface::class, ['connection' => $doctrine]);
    }
}