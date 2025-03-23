<?php
namespace Discord\Proibida;
require_once __DIR__ . '/../vendor/autoload.php';

use Discord\Parts\Interactions\Command\Permission as CommandPermission;
use Discord\Parts\Permissions\Permission;

function random(): float {
    $bias = mt_rand() / mt_getrandmax();
    $biased = pow($bias, 3) * 100; 
    return $biased;
}
function extractId(string $raw) : string|false {
    if(preg_match('/<@!?([\w]+)>/', $raw, $matches)){
        return $matches[1];
    }
    return false;
}
// var_dump( Permission::ROLE_PERMISSIONS['administrator']);

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

echo extractAkuma("+set-akuma <@32432432432> Goro Goro no Mi");

