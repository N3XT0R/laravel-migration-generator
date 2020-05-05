<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Resolver;


use Doctrine\DBAL\Connection as DoctrineConnection;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\DefinitionInterface;

abstract class AbstractResolver implements DefinitionResolverInterface
{
    protected $doctrineConnection;
    protected $definitions = [];

    public function __construct(DoctrineConnection $connection, array $definitions)
    {
        if ($this instanceof DoctrineTypeMappingsInterface) {
            $this->registerDoctrineTypeMappings($connection);
        }
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

    public function addDefinition(string $name, array $definition): void
    {
        $this->definitions[$name] = $definition;
    }

    public function getDefinitionByName(string $name): ?DefinitionInterface
    {
        $definition = null;
        $definitions = $this->getDefinitions();

        if (array_key_exists($name, $definitions) && array_key_exists('class', $definitions[$name]) && app()->has(
                $definitions[$name]['class']
            )) {
            /**
             * @var DefinitionInterface $definition
             */
            $definition = app()->make($definitions[$name]['class']);
        }


        return $definition;
    }

    public function hasDefinition(string $name): bool
    {
        return null !== $this->getDefinitionByName($name);
    }

    public function setDoctrineConnection(DoctrineConnection $doctrineConnection): void
    {
        $this->doctrineConnection = $doctrineConnection;
    }

    public function getDoctrineConnection(): DoctrineConnection
    {
        return $this->doctrineConnection;
    }
}