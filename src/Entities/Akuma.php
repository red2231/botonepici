<?php

namespace Discord\Proibida\Entities;


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'akuma')]
class Akuma
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 191)]
    private string $name;

    #[ORM\Column(type: 'string', enumType: Raridade::class)]
    private Raridade $raridade;

    #[ORM\Column(type: 'string', enumType: Tipo::class)]
    private Tipo $tipo;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\OneToOne(targetEntity: Usuario::class, inversedBy: 'akuma')]
    #[ORM\JoinColumn(name: 'usuario_id', referencedColumnName: 'id', unique: true)]
    private ?Usuario $user = null;


    public function __construct(?Usuario $user = null)
    {
        if ($user !== null) {
            $this->user = $user;
            $user->setAkuma($this);
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