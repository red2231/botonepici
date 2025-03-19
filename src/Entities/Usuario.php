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

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function setAkuma(Akuma $akuma): void
    {
        if ($this->akuma !== $akuma) {
            $this->akuma = $akuma;
            $akuma->user = $this;
        }
    }
    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new \Exception("Propriedade {$name} não existe na classe " . __CLASS__);
    }

    public function __set(string $name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
            return;
        }}
}