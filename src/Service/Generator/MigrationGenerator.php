<?php


namespace N3XT0R\MigrationGenerator\Service\Generator;

use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompilerInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolverInterface;

class MigrationGenerator implements MigrationGeneratorInterface
{

    protected $resolver;
    protected $compiler;
    protected $migrationDir = '';

    public function __construct(DefinitionResolverInterface $resolver, MigrationCompilerInterface $compiler)
    {
        $this->setMigrationDir(database_path() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR);
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

    /**
     * @return string
     */
    public function getMigrationDir(): string
    {
        return $this->migrationDir;
    }

    /**
     * @param string $migrationDir
     */
    public function setMigrationDir(string $migrationDir): void
    {
        $this->migrationDir = $migrationDir;
    }


    public function generateMigrationForTable(string $database, string $table): bool
    {
        $result = false;
        $resolver = $this->getResolver();
        $compiler = $this->getCompiler();
        $table = 'da_attributevalue';
        $schemaResult = $resolver->resolveTableSchema($database, $table);

        $compiler->generateByResult($schemaResult);

        return $compiler->writeToDisk('Create' . ucfirst($table) . 'Table', $this->getMigrationDir());
    }
}