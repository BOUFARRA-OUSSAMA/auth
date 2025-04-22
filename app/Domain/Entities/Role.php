<?php

namespace App\Domain\Entities;

class Role
{
    private ?int $id = null;
    private string $name;
    private string $code;
    private ?string $description;
    private array $permissions = [];

    public function __construct(string $name, string $code, ?string $description = null)
    {
        $this->name = $name;
        $this->code = $code;
        $this->description = $description;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get the permissions associated with this role
     * 
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Set the permissions for this role
     * 
     * @param array $permissions Array of Permission entities
     * @return void
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    /**
     * Add a permission to this role
     * 
     * @param Permission $permission
     * @return void
     */
    public function addPermission(Permission $permission): void
    {
        $this->permissions[] = $permission;
    }

    /**
     * Check if this role has a specific permission by code
     * 
     * @param string $permissionCode
     * @return bool
     */
    public function hasPermission(string $permissionCode): bool
    {
        foreach ($this->permissions as $permission) {
            if ($permission->getCode() === $permissionCode) {
                return true;
            }
        }

        return false;
    }
}
