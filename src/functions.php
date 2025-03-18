<?php


 function getAnimalRaridade()
{
$value = rand_float(0, 100);

switch (true) {
    case ($value <= 60):
        return "Comum";
    case ($value > 60 && $value <= 80):
        return "Incomum";
    case ($value > 80 && $value <= 90):
        return "Rara";
    case ($value > 90 && $value <= 95):
        return "Épica";
    case ($value > 95 && $value <= 99):
        return "Lendária";
    case ($value > 99 && $value <= 99.9):
        return "Mitíca";
    case ($value > 99.9):
        return "Divina";
}

}



function rand_float(float $min, float $max): float {
    if ($min > $max) {
        [$min, $max] = [$max, $min];
    }
    $fraction = mt_rand() / mt_getrandmax();
    return $min + $fraction * ($max - $min);
}
