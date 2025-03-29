<?php
namespace Discord\Proibida;


function random(): float {
    $bias = mt_rand() / mt_getrandmax();
    $biased = pow($bias, 2.7) * 100; 
    return $biased;
}
function extractId(string $raw) : string|false {
    if(preg_match('/<@!?([\w]+)>/', $raw, $matches)){
        return $matches[1];
    }
    return false;
}

function extractAmount(string $raw){
    $partes = explode(' ', $raw);
    $quantidade = $partes[2];
    return $quantidade;
}
function extractAkuma(string $raw) : string {
    $partes = explode(' ', $raw);
    $akuma = implode(' ', array_slice($partes, 2));
    return $akuma;
}

