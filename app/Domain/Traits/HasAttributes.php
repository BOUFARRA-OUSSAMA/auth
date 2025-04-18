<?php

namespace App\Domain\Traits;

trait HasAttributes
{
    /**
     * Get an attribute value
     *
     * @param string $attributeCode
     * @return mixed|null
     */
    public function getAttribute(string $attributeCode)
    {
        // This will be implemented by the repository
        // For use with repositories that support EAV
        return app('eav.service')->getAttributeValue(
            $this->getEntityTypeCode(),
            $this->getId(),
            $attributeCode
        );
    }

    /**
     * Set an attribute value
     *
     * @param string $attributeCode
     * @param mixed $value
     * @return void
     */
    public function setAttribute(string $attributeCode, $value): void
    {
        app('eav.service')->setAttributeValue(
            $this->getEntityTypeCode(),
            $this->getId(),
            $attributeCode,
            $value
        );
    }

    /**
     * Check if an attribute exists
     *
     * @param string $attributeCode
     * @return bool
     */
    public function hasAttribute(string $attributeCode): bool
    {
        return app('eav.service')->attributeExists($attributeCode);
    }

    /**
     * Get all attributes for this entity
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return app('eav.service')->getAttributeValues(
            $this->getEntityTypeCode(),
            $this->getId()
        );
    }

    /**
     * Get the entity type code
     * 
     * @return string
     */
    abstract public function getEntityTypeCode(): string;
}
