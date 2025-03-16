<?php
namespace Discord\Proibida;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Predis\Client;

require_once __DIR__.'/../vendor/autoload.php';
$dotenv = Dotenv::createMutable(__DIR__ . '/../');
$dotenv->safeLoad();
$host = $_ENV['HOST'];
$username = $_ENV['USERNAME'];
$password = $_ENV['PASSWO'];
function exists(string $userId): bool {
    $client = new Client([
        'host' => $GLOBALS['host'],
        'port' => 6379,
        'username' => $GLOBALS['username'],
        'password' => $GLOBALS['password']
    ]);
    
    return $client->exists($userId) === 1;
}

}
function check(string $userId){
    $client = new Client([
        'host' => $GLOBALS['host'],
        'port' => 6379,
        'username' => $GLOBALS['username'],
        'password' => $GLOBALS['password']
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

