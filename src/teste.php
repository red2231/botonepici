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
function extractId(string $raw) : string {
    if(preg_match('/<@!?(\d+)>/', $raw, $matches)){
        return $matches[1];
    }
    return false;
}
// var_dump( Permission::ROLE_PERMISSIONS['administrator']);

function extractAmount(string $raw) : int {
    $partes = explode(' ', $raw);
    $quantidade = $partes[2];
    return $quantidade;
}

echo extractAmount("+add-roll <@2432424324234234> 5");

