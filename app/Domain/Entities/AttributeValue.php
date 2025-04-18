<?php

namespace App\Domain\Entities;

class AttributeValue
{
    private ?int $id;
    private Attribute $attribute;
    private string $entityType;
    private int $entityId;
    private string $value;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt;

    public function __construct(
        Attribute $attribute,
        string $entityType,
        int $entityId,
        string $value,
        ?int $id = null
    ) {
        $this->attribute = $attribute;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->value = $value;
        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
        $this->touch();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Get the typed value based on attribute type
     * 
     * @return mixed
     */
    public function getTypedValue()
    {
        $type = $this->attribute->getType();

        return match ($type) {
            Attribute::TYPE_INTEGER => (int) $this->value,
            Attribute::TYPE_DECIMAL => (float) $this->value,
            Attribute::TYPE_BOOLEAN => (bool) $this->value,
            Attribute::TYPE_DATE, Attribute::TYPE_DATETIME => new \DateTimeImmutable($this->value),
            Attribute::TYPE_JSON => json_decode($this->value, true),
            default => $this->value,
        };
    }
}
