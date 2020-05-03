<?php

namespace Tests\Unit\Service\Generator\Compiler;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use Tests\TestCase;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompiler;
use Illuminate\Contracts\View\Factory as ViewFactory;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper;

class MigrationCompilerTest extends TestCase
{
    protected $compiler;

    public function setUp(): void
    {
        parent::setUp();
        $view = $this->app->make(ViewFactory::class);
        $view->addExtension(
            'stub',
            'replace'
        );
        $compiler = new MigrationCompiler($view, $this->app->make('files'));
        $compiler->setMapper(
            [
                'table' => [
                    'class' => Mapper\FieldMapper::class,
                    'requires' => [],
                ],
                'index' => [
                    'class' => Mapper\IndexMapper::class,
                    'requires' => ['table'],
                ],
            ]
        );
        $this->compiler = $compiler;
    }

    public function testCreateMigrationClass(): void
    {
        $result = new ResultEntity();
        $result->setTableName('test');
        $field = new FieldEntity();
        $field->setColumnName('id');
        $field->setType('bigInteger');
        $field->setComment('fuck that shit');
        $field->setTable('da_attributevalue');
        $field->setArguments(['default' => 1, 'nullable' => 1, 'autoIncrement' => 1]);

        $result->setResultByKey('table', [$field]);

        $this->compiler->generateByResult($result);
        print_r($this->compiler->getRenderedTemplate());
    }
}