<?php
namespace Discord\Proibida;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Predis\Client;

require_once __DIR__.'/../vendor/autoload.php';
$dotenv = Dotenv::createMutable(__DIR__ . '/../');
$dotenv->safeLoad();
$host = $_ENV['host'];

function exists(string $userId):bool|string{
return (new Client([
    'host' => $GLOBALS['host'],
    'port' => 6379
]))->exists($userId);
}
function check(string $userId){
    $client = new Client([
        'host' => $GLOBALS['host'],
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
    return true;
   }

$valor = check('olaaaa');
var_dump($valor);

