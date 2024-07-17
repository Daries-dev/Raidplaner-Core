<?php

namespace rp\system\character;

/**
 * Represents an available character with an ID, name, classification ID, and role ID.
 *
 * The AvailableCharacter class is immutable and provides methods to access its properties.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class AvailableCharacter
{
    /**
     * Constructor for the AvailableCharacter class.
     *
     * @param int|string $id The unique identifier of the character. It can be an integer or a string.
     * @param string $name The name of the character.
     * @param int|null $classificationID The classification id of the character. It is optional and can be null.
     * @param int|null $roleID The role id of the character. It is optional and can be null.
     */
    public function __construct(
        private readonly int|string $id,
        private readonly string $name,
        private readonly ?int $classificationID = null,
        private readonly ?int $roleID = null
    ) {
    }

    /**
     * Return the classification id of the character.
     */
    public function getClassificationID(): ?int
    {
        return $this->classificationID;
    }

    /**
     * Return the unique identifier of the character.
     */
    public function getID(): int|string
    {
        return $this->id;
    }

    /**
     * Return the name of the character.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return the role id of the character.
     */
    public function getRoleID(): ?int
    {
        return $this->roleID;
    }
}
