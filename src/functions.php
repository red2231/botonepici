<?php

use Random\Randomizer;

 function getAnimalRaridade()
{
$value = rand_float(0, 100);
$resultado= null;
 switch ($value){
case $value<=50:
    return "Comum";
case $value>50 && $value<=80:
    return "Incomum";
    case $value<80 && $value<=90:
        return "Rara";
        case $value> 90 && $value<=95:
            return "Épica";
            case $value> 95 && $value<=99:
                return "Lendária";
                case $value>99 && $value<=99.9:
                    return "Mitíca";
                    case $value>99.9:
                        return "Divina";
                        default: "nada";
}
}


function rand_float(int $min, int $max): float{
return $min + mt_rand()/mt_getrandmax() * ($max-$min);
}
$value = getAnimalRaridade();

echo $value;