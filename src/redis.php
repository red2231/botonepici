<?php
namespace Discord\Proibida;
use Carbon\Carbon;
use Predis\Client;
use React\Promise\PromiseInterface;

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

