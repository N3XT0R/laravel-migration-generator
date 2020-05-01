<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition\Traits;


trait AttributeAwareTrait
{
    protected $attributes;

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

    public function GetAttributeByName(string $attribute)
    {
        $attributeValue = null;
        if ($this->hasAttribute($attribute)) {
            $attributeValue = $this->getAttributes()[$attribute];
        }

        return $attributeValue;
    }
}