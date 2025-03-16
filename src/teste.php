<?php
namespace Discord\Proibida;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Predis\Client;

require_once __DIR__.'/../vendor/autoload.php';
$dotenv = Dotenv::createMutable(__DIR__ . '/../');
$dotenv->safeLoad();
$username = $_ENV['USERNAME'];
$password = $_ENV['PASSWO'];


function check(string $userId): true|string{
    $client = new Client([
        'host' => 'redis.railway.internal',
        'port' => 6379,
        'username' => 'default',
        'password' => 'GFWjLTfOOglzWRJsoRlQmKkFnsOheolO'
    ]);
    
    if(!$client->get($userId)){
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
   

