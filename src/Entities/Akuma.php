<?php

namespace Discord\Proibida\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Akuma
{
    #[ORM\Id]
    private int $id;

    public function __construct(    #[ORM\Column(type: 'string')]
    private string $name,
    
    ) {
        $this->var = $var;
    }
}