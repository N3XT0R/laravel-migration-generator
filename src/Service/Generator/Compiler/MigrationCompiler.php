<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use Illuminate\Contracts\View\Factory as ViewFactory;
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

    public function generateByResult(ResultEntity $resultEntity): void
    {
    }
}