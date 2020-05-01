<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition\Traits;


interface AttributeAwareInterface
{
    public function getAttributes(): array;

    public function setAttributes(array $attributes): void;

    public function hasAttribute(string $attribute): bool;

    public function addAttribute(string $key, $value): void;

    public function GetAttributeByName(string $attribute);
}