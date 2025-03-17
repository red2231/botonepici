<?php

use Random\Randomizer;

 function getAnimalRaridade()
{
$value = rand_float(0, 100);

    $resultado = match (true) {
        $value <= 50 => 'Comum',
        $value <= 80 => 'Incomum',
        $value <= 90 => 'Rara',
        $value <= 95 => 'Épica',
        $value <= 99 => 'Lendária',
        $value <= 99.9 => 'Mítica',
        $value > 99.9 => 'Divina',
    };
return $resultado;
}



function rand_float(int $min, int $max): float{
return $min + mt_rand()/mt_getrandmax() * ($max-$min);
}
$value = getAnimalRaridade();

echo $value;