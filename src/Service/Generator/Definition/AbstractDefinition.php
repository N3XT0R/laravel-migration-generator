<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Traits\AttributeAwareInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Traits\AttributeAwareTrait;

abstract class AbstractDefinition implements DefinitionInterface, AttributeAwareInterface
{
    protected $result = [];
    protected $schema;

    use AttributeAwareTrait;

    public function setSchema(AbstractSchemaManager $schema): void
    {
        $this->schema = $schema;
    }

    public function getSchema(): AbstractSchemaManager
    {
        return $this->schema;
    }

    public function hasSchema(): bool
    {
        return null !== $this->schema;
    }

    abstract protected function generateData(): void;

    public function generate(): void
    {
        if (!$this->hasSchema()) {
            throw new \RuntimeException('missing Schema on Definition!');
        }

        $this->generateData();
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function setResult(array $result): void
    {
        $this->result = $result;
    }


}