<?php


namespace N3XT0R\MigrationGenerator\Service\Generator;

use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompilerInterface;
use N3XT0R\MigrationGenerator\Service\Generator\DTO\MigrationTimingDto;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolverInterface;

class MigrationGenerator implements MigrationGeneratorInterface
{

    protected DefinitionResolverInterface $resolver;
    protected MigrationCompilerInterface $compiler;
    protected string $migrationDir = '';
    protected array $errorMessages = [];
    protected array $migrationFiles = [];

    public function __construct(DefinitionResolverInterface $resolver, MigrationCompilerInterface $compiler)
    {
        $this->setMigrationDir(database_path().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR);
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
     * @param  string  $migrationDir
     */
    public function setMigrationDir(string $migrationDir): void
    {
        $this->migrationDir = $migrationDir;
    }

    /**
     * @return array
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    /**
     * @param  array  $errorMessages
     */
    public function setErrorMessages(array $errorMessages): void
    {
        $this->errorMessages = $errorMessages;
    }

    public function addErrorMessage(string $errorMessage): void
    {
        $this->errorMessages[] = $errorMessage;
    }

    /**
     * @return array
     */
    public function getMigrationFiles(): array
    {
        return $this->migrationFiles;
    }

    /**
     * @param  array  $migrationFiles
     */
    public function setMigrationFiles(array $migrationFiles): void
    {
        $this->migrationFiles = $migrationFiles;
    }

    public function generateMigrationForTable(
        string $database,
        string $table,
        MigrationTimingDto $timingDto = new MigrationTimingDto()
    ): bool {
        $this->setErrorMessages([]);
        $result = false;
        $resolver = $this->getResolver();
        $compiler = $this->getCompiler();

        try {
            $schemaResult = $resolver->resolveTableSchema($database, $table);
            $compiler->generateByResult($schemaResult);
            $result = $compiler->writeToDisk(
                'Create'.ucfirst($table).'Table',
                $this->getMigrationDir(),
                $timingDto,
            );
            $this->setMigrationFiles($compiler->getMigrationFiles());
        } catch (\Exception $e) {
            $this->addErrorMessage($e->getMessage());
        }

        return $result;
    }
}