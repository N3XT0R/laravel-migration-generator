<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper\MapperInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\DTO\MigrationTimingDto;
use N3XT0R\MigrationGenerator\Service\Generator\Sort\TopSort;

class MigrationCompiler implements MigrationCompilerInterface
{
    protected ViewFactory $view;
    protected string $renderedTemplate;
    protected array $mapper = [];
    protected Filesystem $filesystem;
    protected array $migrationFiles = [];

    public function __construct(ViewFactory $view, Filesystem $filesystem)
    {
        $this->setView($view);
        $this->setFilesystem($filesystem);
    }

    public function setView(ViewFactory $view): void
    {
        $this->view = $view;
    }

    public function getView(): ViewFactory
    {
        return $this->view;
    }

    /**
     * @return array
     */
    public function getMapper(): array
    {
        return $this->mapper;
    }

    /**
     * @param array $mapper
     */
    public function setMapper(array $mapper): void
    {
        $this->mapper = $mapper;
    }

    public function setRenderedTemplate(string $renderedTemplate): void
    {
        $this->renderedTemplate = $renderedTemplate;
    }

    public function getRenderedTemplate(): string
    {
        return $this->renderedTemplate;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem): void
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return array
     */
    public function getMigrationFiles(): array
    {
        return $this->migrationFiles;
    }

    /**
     * @param array $migrationFiles
     */
    public function setMigrationFiles(array $migrationFiles): void
    {
        $this->migrationFiles = $migrationFiles;
    }

    public function addMigrationFile(string $file): void
    {
        $this->migrationFiles[] = $file;
    }

    protected function render(string $view, array $data = []): string
    {
        $viewFactory = $this->getView();
        $viewFactory->flushFinderCache();
        $viewObj = $viewFactory->make($view, $data);

        return $viewObj->render();
    }

    public function generateByResult(ResultEntity $resultEntity, string $customMigrationClass = ''): void
    {
        $this->setMigrationFiles([]);
        $tableName = $resultEntity->getTableName();
        $mapper = $this->getMapper();
        $sortedMapper = TopSort::sort($mapper);

        $migrationNamespace = $this->resolveMigrationNamespace($customMigrationClass);
        $migrationClass = $this->extractClassFromNamespace($migrationNamespace);
        $columns = $this->collectMappedColumns($sortedMapper, $mapper, $resultEntity, $tableName);


        $data = [
            'migrationNamespace' => $migrationNamespace,
            'migrationClass' => $migrationClass,
            'tableName' => $tableName,
            'columns' => $columns,
        ];

        $this->setRenderedTemplate($this->render('migration-generator::CreateTableStub', $data));
    }

    protected function collectMappedColumns(
        array $sortedMapper,
        array $mapper,
        ResultEntity $resultEntity,
        string $tableName
    ): array {
        $columns = [];

        foreach ($sortedMapper as $mappingName) {
            $mapping = app()->make($mapper[$mappingName]['class']);
            if (!$mapping instanceof MapperInterface) {
                continue;
            }

            $resultData = $resultEntity->getResultByTableNameAndKey($tableName, $mappingName);
            foreach ($mapping->map($resultData) as $line) {
                $columns[] = $line;
            }
        }

        return $columns;
    }


    private function resolveMigrationNamespace(string $customClass): string
    {
        return (!empty($customClass) && class_exists($customClass))
            ? 'use ' . $customClass . ';'
            : 'use ' . Migration::class . ';';
    }

    private function extractClassFromNamespace(string $namespaceLine): string
    {
        $parts = explode('\\', str_replace(';', '', $namespaceLine));
        return end($parts);
    }

    public function writeToDisk(
        string $name,
        string $path,
        MigrationTimingDto $timingDto = new MigrationTimingDto()
    ): bool {
        $result = false;
        $this->setMigrationFiles([]);
        $tpl = $this->getRenderedTemplate();

        if (!empty($tpl)) {
            $fileName = $this->generateFilename(
                $name,
                $timingDto->getCurrentAmount(),
                $timingDto->getMaxAmount(),
                $timingDto->getTimestamp()
            );
            $renderedTemplate = str_replace('DummyClass', Str::studly($name), $tpl);
            $result = $this->writeTemplateToFile($path, $fileName, $renderedTemplate);
        }

        return $result;
    }

    private function generateFilename(string $name, int $currentAmount, int $maxAmount, int $timestamp): string
    {
        $prefix = ($currentAmount !== -1 && $maxAmount !== -1 && $timestamp !== -1)
            ? $this->getHourMinuteSecondPrefix($currentAmount, $maxAmount, $timestamp)
            : date('Y_m_d_His');

        return $prefix . '_' . Str::snake($name) . '.php';
    }

    private function writeTemplateToFile(string $path, string $fileName, string $content): bool
    {
        $filesystem = $this->getFilesystem();
        $filePath = $path . DIRECTORY_SEPARATOR . $fileName;
        $success = false;

        if ($filesystem->exists($path) && !$filesystem->exists($filePath)) {
            $success = $filesystem->put($filePath, $content) > 0;

            if ($success) {
                $this->addMigrationFile($fileName);
            }
        }

        return $success;
    }

    private function getHourMinuteSecondPrefix(int $actual, int $max, int $timestamp): string
    {
        $timestampStart = $timestamp - $max + $actual;
        return date('Y_m_d_His', $timestampStart);
    }
}