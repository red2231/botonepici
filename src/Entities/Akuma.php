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
    #[ORM\JoinColumn(name: 'usuario_id', referencedColumnName: 'id', unique: true, onDelete:"SET NULL", nullable:true)]
    private ?Usuario $user = null;


    public function __construct(?Usuario $user = null)
    {
        if ($user !== null) {
            $this->user = $user;
            $user->setAkuma($this);
        }
    }
public function getUser():Usuario
{
    return $this->user;
}
public function setUser(Usuario $user)
{
    $this->user=$user;
}

    /**
     * Get the value of description
     *
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the value of tipo
     *
     * @return Tipo
     */
    public function getTipo(): Tipo {
        return $this->tipo;
    }

    /**
     * Set the value of tipo
     *
     * @param Tipo $tipo
     *
     * @return self
     */
    public function setTipo(Tipo $tipo): self {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * Get the value of raridade
     *
     * @return Raridade
     */
    public function getRaridade(): Raridade {
        return $this->raridade;
    }

    /**
     * Set the value of raridade
     *
     * @param Raridade $raridade
     *
     * @return self
     */
    public function setRaridade(Raridade $raridade): self {
        $this->raridade = $raridade;
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
    }