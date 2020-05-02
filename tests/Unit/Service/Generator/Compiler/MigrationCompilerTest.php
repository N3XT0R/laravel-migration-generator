<?php

namespace Tests\Unit\Service\Generator\Compiler;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use Tests\TestCase;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompiler;

class MigrationCompilerTest extends TestCase
{
    protected $compiler;

    public function setUp(): void
    {
        parent::setUp();
        $this->compiler = new MigrationCompiler();
    }

    public function testCreateMigrationClass(): void
    {
        $field = new FieldEntity();
        $field->setColumnName('id');
        $field->setType('bigInteger');
        $field->setComment('fuck you');
        $field->setTable('da_attributevalue');
        $field->setArguments(['default' => 1, 'nullable' => 1, 'autoIncrement' => 1]);
        $resultEntity = new ResultEntity();
        $this->compiler->initializeMigration('CreateTableBla');
        $this->compiler->createByFields('da_attributevalue', [$field]);
        var_dump($this->compiler->getContent());
    }
}