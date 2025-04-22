<?php

namespace App\Domain\Entities;

class Role
{
    private ?int $id;
    private string $name;
    private string $code;
    private ?string $description;
    private array $permissions = [];

    /**
     * Role constructor.
     *
     * @param string $name
     * @param string $code
     * @param string|null $description
     * @param int|null $id
     */
    public function __construct(
        string $name,
        string $code,
        ?string $description = null,
        ?int $id = null
    ) {
        $this->name = $name;
        $this->code = $code;
        $this->description = $description;
        $this->id = $id;
    }

    /**
     * Get role ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set role ID
     *
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get role name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set role name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get role code
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Set role code
     *
     * @param string $code
     * @return void
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * Get role description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set role description
     *
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get role permissions
     *
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Set role permissions
     *
     * @param array $permissions
     * @return void
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    /**
     * Add permission to role
     *
     * @param Permission $permission
     * @return void
     */
    public function addPermission(Permission $permission): void
    {
        $this->permissions[] = $permission;
    }
}
