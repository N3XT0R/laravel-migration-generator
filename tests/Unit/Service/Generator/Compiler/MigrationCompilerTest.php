<?php

namespace Tests\Unit\Service\Generator\Compiler;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use Tests\TestCase;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompiler;
use Illuminate\Contracts\View\Factory as ViewFactory;

class MigrationCompilerTest extends TestCase
{
    protected $compiler;

    public function setUp(): void
    {
        parent::setUp();
        $this->compiler = new MigrationCompiler($this->app->make(ViewFactory::class));
    }

    public function testCreateMigrationClass(): void
    {
        $result = new ResultEntity();
        $result->setTableName('test');
        $field = new FieldEntity();
        $field->setColumnName('id');
        $field->setType('bigInteger');
        $field->setComment('fuck you');
        $field->setTable('da_attributevalue');
        $field->setArguments(['default' => 1, 'nullable' => 1, 'autoIncrement' => 1]);

        $result->setResultByKey('table', [$field]);

        $this->compiler->generateByResult($result);
        print_r($this->compiler->getRenderedTemplate());
    }
}