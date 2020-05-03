<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Resolver;


use Doctrine\DBAL\Connection as DoctrineConnection;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\DefinitionInterface;
use MJS\TopSort\Implementations\GroupedStringSort;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;

class DefinitionResolver implements DefinitionResolverInterface
{
    protected $doctrineConnection;
    protected $definitions = [];

    public function __construct(DoctrineConnection $connection, array $definitions)
    {
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

    public function addDefinition(string $name, string $definition): void
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

        /**
         * @var DefinitionInterface $definition
         */
        $definition = app()->make($definition['class']);

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

    public function resolveTableSchema(string $schema, string $table): ResultEntity
    {
        $definitions = $this->getDefinitions();
        $sortedDefinitions = $this->sortDefinitions($definitions);
        $connection = $this->getDoctrineConnection();
        $schemaManager = $connection->getSchemaManager();
        $definitionResult = [];

        foreach ($sortedDefinitions as $name) {
            $definitionClass = $this->getDefinitionByName($name);
            if (null !== $schemaManager && $definitionClass instanceof DefinitionInterface) {
                $dependencies = $definitions[$name]['requires'];
                $definitionClass->setAttributes(
                    [
                        'database' => $schema,
                        'tableName' => $table,
                    ]
                );

                foreach ($dependencies as $dependency) {
                    if (array_key_exists($dependency, $definitionResult[$table])) {
                        $definitionClass->addAttribute($dependency, $definitionResult[$table][$dependency]);
                    }
                }

                $definitionClass->setSchema($schemaManager);
                try {
                    $definitionClass->generate();
                } catch (\Exception $e) {
                    /**
                     * @todo
                     */
                    break;
                }

                if (!array_key_exists($table, $definitionResult)) {
                    $definitionResult[$table] = [];
                }

                $definitionResult[$table][$name] = $definitionClass->getResult();
            }
        }

        $result = new ResultEntity();
        $result->setTableName($table);
        $result->setResults($definitionResult);

        return $result;
    }

    private function sortDefinitions(array $definitions): array
    {
        $topSort = new GroupedStringSort();
        foreach ($definitions as $key => $data) {
            $topSort->add($key, $key, $data['requires']);
        }

        return $topSort->sort();
    }
}