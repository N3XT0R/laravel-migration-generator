<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler;

use Illuminate\Database\Migrations\Migration;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Engine\ReplaceEngine;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use Illuminate\View\Factory as ViewFactory;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;

class MigrationCompiler implements MigrationCompilerInterface
{
    protected $view;
    protected $renderedTemplate;

    public function __construct(ViewFactory $view)
    {
        $this->setView($view);
    }

    public function setView(ViewFactory $view): void
    {
        $this->view = $view;
    }

    public function getView(): ViewFactory
    {
        return $this->view;
    }

    public function setRenderedTemplate(string $renderedTemplate): void
    {
        $this->renderedTemplate = $renderedTemplate;
    }

    public function getRenderedTemplate(): string
    {
        return $this->renderedTemplate;
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

        $data = [
            'migrationNamespace' => 'use ' . Migration::class . ';',
            'migrationClass' => Migration::class,
            'tableName' => $tableName,
            'className' => 'Create' . ucfirst($tableName) . 'Table',
            'columns' => '',
        ];

        if (!empty($customMigrationClass) && class_exists($customMigrationClass)) {
            $data['migrationClass'] = $customMigrationClass;
        }

        $this->setRenderedTemplate($this->render('migration-generator::CreateTableStub', $data));
    }
}