<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler;

use Illuminate\Database\Migrations\Migration;
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

        return $viewFactory->make($view, $data)->render();
    }

    public function generateByResult(ResultEntity $resultEntity, string $customMigrationClass = ''): void
    {
        $tableName = $resultEntity->getTableName();

        $data = [
            'migrationClass' => Migration::class,
            'tableName' => $tableName,
            'classname' => 'Create' . ucfirst($tableName) . 'Table',
            'columns' => '',
        ];

        $this->setRenderedTemplate($this->render('migration-generator.CreateTableStub', $data));
    }
}