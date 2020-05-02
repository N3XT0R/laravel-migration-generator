<?php


namespace N3XT0R\MigrationGenerator\Service\Generator;

use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolverInterface;

class MigrationGenerator implements MigrationGeneratorInterface
{

    protected $resolver;

    public function __construct(DefinitionResolverInterface $resolver)
    {
        $this->setResolver($resolver);
    }

    public function setResolver(DefinitionResolverInterface $resolver): void
    {
        $this->resolver = $resolver;
    }

    public function getResolver(): DefinitionResolverInterface
    {
        return $this->resolver;
    }

    public function generateMigrationForTable(string $database, string $table): bool
    {
        $result = false;
        $resolver = $this->getResolver();
        $table = 'da_attributevalue';
        $schemaResult = $resolver->resolveTableSchema($database, $table);


        return $result;
    }
}