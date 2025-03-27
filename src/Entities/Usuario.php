<?php

namespace Discord\Proibida\Entities;


class Usuario
{

    private int $id;
    
    private string $username;
    private string $avatarUrl;
    private int $rolls;
    public function __construct(?string $username =null)
    {
        if($username){
            $this->username = $username;
        }
      
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
}