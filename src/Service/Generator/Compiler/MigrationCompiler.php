<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler;

use Illuminate\Database\Migrations\Migration;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper\MapperInterface;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\Filesystem\Filesystem;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Sort\TopSort;
use Illuminate\Support\Str;

class MigrationCompiler implements MigrationCompilerInterface
{
    protected $view;
    protected $renderedTemplate;
    protected $mapper = [];
    protected $filesystem;

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

    protected function render(string $view, array $data = []): string
    {
        $viewFactory = $this->getView();
        $viewFactory->flushFinderCache();
        $viewObj = $viewFactory->make($view, $data);

        return $viewObj->render();
    }

    public function generateByResult(ResultEntity $resultEntity, string $customMigrationClass = ''): void
    {
        $tableName = $resultEntity->getTableName();
        $mapper = $this->getMapper();
        $sortedMapper = TopSort::sort($mapper);

        $data = [
            'migrationNamespace' => 'use ' . Migration::class . ';',
            'tableName' => $tableName,
            'className' => 'DummyClass',
            'columns' => [],
        ];

        if (!empty($customMigrationClass) && class_exists($customMigrationClass)) {
            $data['migrationNamespace'] = 'use ' . $customMigrationClass . ';';
        } else {
            $data['migrationNamespace'] = 'use ' . Migration::class . ';';
        }

        $namespaceParts = explode('\\', str_replace(';', '', $data['migrationNamespace']));
        $data['migrationClass'] = $namespaceParts[count($namespaceParts) - 1];

        foreach ($sortedMapper as $key => $mappingName) {
            $mapping = app()->make($mapper[$mappingName]['class']);
            if ($mapping instanceof MapperInterface) {
                $resultData = $resultEntity->getResultByKey($mappingName);
                $extractedLines = $mapping->map($resultData);
                $data['columns'] = array_merge($data['columns'], $extractedLines);
            }
        }

        $this->setRenderedTemplate($this->render('migration-generator::CreateTableStub', $data));
    }

    public function writeToDisk(string $name, string $path, bool $clearFolder = false): bool
    {
        $result = false;
        $filesystem = $this->getFilesystem();
        $fileName = date('Y_m_d_His') . '_' . Str::snake($name) . '.php';
        $renderedTemplate = str_replace('DummyClass', $name, $this->getRenderedTemplate());

        if ($filesystem->exists($path)) {
            $fileLocation = $path . DIRECTORY_SEPARATOR . $fileName;
            if (false === $filesystem->exists($fileLocation)) {
                $result = $filesystem->put($fileLocation, $renderedTemplate) > 0;
            }
        }

        return $result;
    }
}