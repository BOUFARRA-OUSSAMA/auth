<?php

namespace App\Domain\Entities;

class Permission
{
    private ?int $id;
    private string $name;
    private string $code;
    private ?string $description;
    private ?string $group;

    /**
     * Permission constructor.
     *
     * @param string $name
     * @param string $code
     * @param string|null $description
     * @param string|null $group
     * @param int|null $id
     */
    public function __construct(
        string $name,
        string $code,
        ?string $description = null,
        ?string $group = null,
        ?int $id = null
    ) {
        $this->name = $name;
        $this->code = $code;
        $this->description = $description;
        $this->group = $group;
        $this->id = $id;
    }

    /**
     * Get permission ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set permission ID
     *
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get permission name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set permission name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get permission code
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Set permission code
     *
     * @param string $code
     * @return void
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * Get permission description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set permission description
     *
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get permission group
     *
     * @return string|null
     */
    public function getGroup(): ?string
    {
        return $this->group;
    }

    /**
     * Set permission group
     *
     * @param string|null $group
     * @return void
     */
    public function setGroup(?string $group): void
    {
        $this->group = $group;
    }
}
