<?php
namespace Discord\Proibida;
use Carbon\Carbon;
use Dotenv\Dotenv;
use Predis\Client;
use React\Promise\PromiseInterface;

require_once __DIR__.'/../vendor/autoload.php';
$dotenv = Dotenv::createMutable(__DIR__ . '/../');
$dotenv->safeLoad();

function check(string $userId, AkumaManager $manager):PromiseInterface{
 return $manager->hasAkuma($userId)->then(function(bool $bool) use ($userId){
    $client = new Client([
        'host' => $_ENV['HOST'] ?? 'localhost',
        'port' => 6379,
        'username' => $_ENV['USERNAME'] ?? null,
        'password' => $_ENV['PASSWO'] ?? null,
    ]);

   
    $tempo = $bool ? 129600 : 86400;
    
    $storedTimestamp = $client->get($userId);
    if (!$storedTimestamp) {
        $client->setex($userId, $tempo, Carbon::now()->timestamp);
        return true;
    }

   
    $expirationTime = Carbon::createFromTimestamp($storedTimestamp)->addDay();
    if ($bool) {
        $expirationTime->addHours(12);
    }
    
    $now = Carbon::now();
    if ($now->lessThan($expirationTime)) {
        return $now->diffAsCarbonInterval($expirationTime)
            ->locale('pt_BR')
            ->forHumans(['parts' => 3]);
    }

    $client->del($userId);
    return true;
  });
  
}
