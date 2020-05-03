<?php


namespace N3XT0R\MigrationGenerator\Service\Generator;

use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompilerInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolverInterface;

class MigrationGenerator implements MigrationGeneratorInterface
{

    protected $resolver;
    protected $compiler;

    public function __construct(DefinitionResolverInterface $resolver, MigrationCompilerInterface $compiler)
    {
        $this->setResolver($resolver);
        $this->setCompiler($compiler);
    }

    public function setResolver(DefinitionResolverInterface $resolver): void
    {
        $this->resolver = $resolver;
    }

    public function getResolver(): DefinitionResolverInterface
    {
        return $this->resolver;
    }

    public function setCompiler(MigrationCompilerInterface $compiler): void
    {
        $this->compiler = $compiler;
    }

    public function getCompiler(): MigrationCompilerInterface
    {
        return $this->compiler;
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