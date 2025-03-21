<?php

namespace Discord\Proibida\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class AkumaToAdd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;
    #[ORM\Column]
    private string $name;
    #[ORM\Column(nullable:true, type:'string')]
    private ?string $description;

    #[ORM\Column(name:'userId', type:'string')]
    private string $userId;
    #[ORM\Column(name:'avatarUser', type:'string')]
    private string $avatarUser;


    /**
     * Get the value of description
     *
     * @return ?string
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param ?string $description
     *
     * @return self
     */
    public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the value of name
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the value of id
     *
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param int $id
     *
     * @return self
     */
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the value of userId
     *
     * @return string
     */
    public function getUserId(): string {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @param string $userId
     *
     * @return self
     */
    public function setUserId(string $userId): self {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Get the value of avatarUser
     *
     * @return string
     */
    public function getAvatarUser(): string {
        return $this->avatarUser;
    }

    /**
     * Set the value of avatarUser
     *
     * @param string $avatarUser
     *
     * @return self
     */
    public function setAvatarUser(string $avatarUser): self {
        $this->avatarUser = $avatarUser;
        return $this;
    }
}