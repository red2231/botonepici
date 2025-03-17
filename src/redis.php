<?php
namespace Discord\Proibida;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Predis\Client;

require_once __DIR__.'/../vendor/autoload.php';
$dotenv = Dotenv::createMutable(__DIR__ . '/../');
$dotenv->safeLoad();
$username = $_ENV['USERNAME']??null;
$password = $_ENV['PASSWO']??null;
$host = $_ENV['HOST']??'localhost';

function check(string $userId): bool|string {
    $client = new Client([
        'host' => $GLOBALS['host'],
        'port' => 6379,
        'username' => $GLOBALS['username'],
        'password' => $GLOBALS['password']
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

$check = check('ola');
var_dump($check);