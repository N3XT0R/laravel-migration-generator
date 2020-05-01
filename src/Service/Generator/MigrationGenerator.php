<?php


namespace N3XT0R\MigrationGenerator\Service\Generator;

use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\Connection as DoctrineConnection;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\DefinitionInterface;

class MigrationGenerator implements MigrationGeneratorInterface
{

    protected $doctrineConnection;
    protected $definitions = [];

    public function __construct(string $connectionName, array $definitions)
    {
        $connection = DB::connection($connectionName)->getDoctrineConnection();
        $this->registerDoctrineTypeMappings($connection);
        $this->setDoctrineConnection($connection);
        $this->setDefinitions($definitions);
    }

    public function setDefinitions(array $definitions): void
    {
        $this->definitions = $definitions;
    }

    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    public function addDefinition(string $name, DefinitionInterface $definition): void
    {
        $this->definitions[$name] = $definition;
    }

    public function getDefinitionByName(string $name): ?DefinitionInterface
    {
        $definition = null;
        $definitions = $this->getDefinitions();

        if (array_key_exists($name, $definitions)) {
            $definition = $definitions[$name];
        }

        return $definition;
    }

    public function hasDefinition(string $name): bool
    {
        return null !== $this->getDefinitionByName($name);
    }

    protected function registerDoctrineTypeMappings(DoctrineConnection $doctrineConnection): void
    {
    }

    public function setDoctrineConnection(DoctrineConnection $doctrineConnection): void
    {
        $this->doctrineConnection = $doctrineConnection;
    }

    public function getDoctrineConnection(): DoctrineConnection
    {
        return $this->doctrineConnection;
    }


    public function generateMigrationForTable(string $table)
    {
        $definitions = $this->getDefinitions();
        $connection = $this->getDoctrineConnection();
        $schema = $connection->getSchemaManager();

        foreach ($definitions as $definition) {
            if (null !== $schema && $definition instanceof DefinitionInterface) {
                $definition->setSchema($schema);
                $definition->generate();
            }
        }
    }
}