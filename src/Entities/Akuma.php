<?php

namespace Discord\Proibida\Entities;


class Akuma
{

    private int $id;

    private string $name;

    private Raridade $raridade;

    private Tipo $tipo;

    private string $description;


    private ?int $usuario_id = null;
public function getUsuario_id():int
{
    return $this->usuario_id;
}
public function setUsuario_id(int $id)
{
    $this->usuario_id=$id;
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
    public function setTipo(string $tip): self {
        $this->tipo = Tipo::from($tip);
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
    public function setRaridade(string $raridad): self {
        $this->raridade = Raridade::from($raridad);
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
    public function setId(string $id): self {
        $this->id = (int)$id;
        return $this;
    }
    }