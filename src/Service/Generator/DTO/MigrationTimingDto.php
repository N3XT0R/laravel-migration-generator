<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\DTO;

final class MigrationTimingDto
{
    private int $currentAmount;
    private int $maxAmount;
    private int $timestamp;

    public function __construct(
        int $currentAmount = -1,
        int $maxAmount = -1,
        int $timestamp = -1
    ) {
        $this->setCurrentAmount($currentAmount);
        $this->setMaxAmount($maxAmount);
        $this->setTimestamp($timestamp);
    }

    public function getCurrentAmount(): int
    {
        return $this->currentAmount;
    }

    public function setCurrentAmount(int $currentAmount): self
    {
        $this->currentAmount = $currentAmount;
        return $this;
    }

    public function getMaxAmount(): int
    {
        return $this->maxAmount;
    }

    public function setMaxAmount(int $maxAmount): self
    {
        $this->maxAmount = $maxAmount;
        return $this;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}
