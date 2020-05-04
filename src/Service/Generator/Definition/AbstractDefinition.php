<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;

use Doctrine\DBAL\Schema\AbstractSchemaManager;

abstract class AbstractDefinition implements DefinitionInterface
{
    protected $result = [];
    protected $schema;
    protected $attributes = [];

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->getAttributes());
    }

    public function addAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttributeByName(string $attribute)
    {
        $attributeValue = null;
        if ($this->hasAttribute($attribute)) {
            $attributeValue = $this->getAttributes()[$attribute];
        }

        return $attributeValue;
    }

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

    abstract protected function generateData(): array;

    public function generate(): void
    {
        if (!$this->hasSchema()) {
            throw new \RuntimeException('missing Schema on Definition!');
        }

        $this->setResult($this->generateData());
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