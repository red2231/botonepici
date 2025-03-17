<?php
namespace Discord\Proibida;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Predis\Client;

require_once __DIR__.'/../vendor/autoload.php';
$dotenv = Dotenv::createMutable(__DIR__ . '/../');
$dotenv->safeLoad();

function check(string $userId): bool|string {
    $client = new Client([
        'host' => $_ENV['HOST']??'localhost',
        'port' => 6379,
        'username' => $_ENV['USERNAME']??null,
        'password' => $_ENV['PASSWO']??null,
        
    ]);

    $storedTimestamp = $client->get($userId);

    if (!$storedTimestamp) {
        $client->setex($userId, 86400, Carbon::now()->timestamp);
        return true;
    }

    $expirationTime = Carbon::createFromTimestamp($storedTimestamp)->addDay();
    $now = Carbon::now();

    if ($now->lessThan($expirationTime)) {
        return $now->diffAsCarbonInterval($expirationTime)
            ->locale('pt_BR')
            ->forHumans(['parts' => 3]);
            
    }

    $client->del($userId);
    return true;
}

function getRaridaded(String $UserID) {
    $client = new Client([
        'host' => $_ENV['HOST']??'localhost',
        'port' => 6379,
        'username' => $_ENV['USERNAME']??null,
        'password' => $_ENV['PASSWO']??null,
        'prefix' => 'app:'
    ]);
    $storedTimestamp = $client->get("user:".$UserID);
    if(!$storedTimestamp){
        $client->set("user:{$UserID}", Carbon::now()->timestamp);
return true;
    }

    $expirationTime = Carbon::createFromTimestamp($storedTimestamp)->addMinutes(30);
    $now = Carbon::now();

    if ($now->lessThan($expirationTime)) {
        return $now->diffAsCarbonInterval($expirationTime)
            ->locale('pt_BR')
            ->forHumans(['parts' => 2]);
            
    }

    $client->del("user:".$UserID);
    return true;
}