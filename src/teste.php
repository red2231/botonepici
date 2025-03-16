<?php
namespace Discord\Proibida;
use Carbon\Carbon;
use Predis\Client;

require_once __DIR__.'/../vendor/autoload.php';


function exists(string $userId):bool|string{
return (new Client([
    'host' => 'localhost',
    'port' => 6379
]))->exists($userId);
}
function check(string $userId){
    $client = new Client([
        'host' => 'localhost',
        'port' => 6379
    ]);

    if(!exists($userId)){
        $client->setex($userId, 86400, Carbon::now()->timestamp);
return true;
    }

    $storedTimestamp = $client->get($userId);
    


    $expirationTime = Carbon::createFromTimestamp($storedTimestamp)->addDay();
    $now = Carbon::now();

    if ($now->lessThan($expirationTime)) {
        return $now->diff($expirationTime)->__toString();
    }
    
   }

$valor = check('olaaaa');
var_dump($valor);

