<?php

namespace Discord\Proibida\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usuario', indexes: [new ORM\Index(name: "idx_username", columns: ['username'])])]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;
    
    #[ORM\Column(type: 'string', unique: true, length: 100)]
    private string $username;
    #[ORM\Column(type:'string', length: 500)]
    private string $avatarUrl;
    #[ORM\OneToOne(targetEntity: Akuma::class, mappedBy: 'user', cascade: ['persist', 'refresh', 'detach'])]
    private ?Akuma $akuma = null;
    #[ORM\Column(type:'integer')]
    private int $rolls =0;
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function setAkuma(Akuma $akuma): void
    {
        if ($this->akuma !== $akuma) {
            $this->akuma = $akuma;
            $akuma->setUser($this);
            }
    }
   public function getAkuma():?Akuma
   {
    return $this->akuma;
   }

    /**
     * Get the value of username
     *
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * Set the value of avatarUrl
     *
     * @param string $avatarUrl
     *
     * @return self
     */
    public function setAvatarUrl(string $avatarUrl): self {
        $this->avatarUrl = $avatarUrl;
        return $this;
    }

    /**
     * Get the value of avatarUrl
     *
     * @return string
     */
    public function getAvatarUrl(): string {
        return $this->avatarUrl;
    }

    /**
     * Set the value of username
     *
     * @param string $username
     *
     * @return self
     */
    public function setUsername(string $username): self {
        $this->username = $username;
        return $this;
    }

    public function setRolls(int $quantidade)
    {
        $this->rolls+=$quantidade;
    }
    public  function getRolls():int
    {
        return $this->rolls;
    }
}