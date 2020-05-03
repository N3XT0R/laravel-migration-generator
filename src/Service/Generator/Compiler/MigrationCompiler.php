<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use Illuminate\Contracts\View\Factory as ViewFactory;

class MigrationCompiler implements MigrationCompilerInterface
{
    protected $view;

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
}