<?php

namespace Discord\Proibida;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/rb-mysql.php';
require_once __DIR__.'/U.php';
use Discord\Discord as bot;
use R;



use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use \Dotenv\Dotenv as env;

$en = env::createImmutable(__DIR__ . '/../');
$en->safeLoad();
$token = $_ENV['TOKEN'];
$dbName=$_ENV['DB_NAME'];
$user=$_ENV['USER'];
$password=$_ENV['PASSWORD'];

if (!R::testConnection()) {
    R::setup("mysql:host=172.18.0.2;dbname={$dbName}, {$user}, {$password}");
}
// Test database (optional)
$pessoa = R::dispense('pessoa');
$pessoa->nome = "erick";
R::store($pessoa);

$discord = new bot([
    'token' => $token,
    'intents' => [Intents::GUILD_MESSAGES, Intents::MESSAGE_CONTENT],
    'shardId' => 0,
    'shardCount' => 5
]);

$discord->on('init', function (bot $discord) {
    echo 'Bot iniciou' . PHP_EOL;
});

$discord->on(Event::MESSAGE_CREATE, function (Message $message, bot $discord) {
    if ($message->author->bot) {
        return;
    }
    if ($message->content === "!roll") {
        $message->channel->sendMessage("<@{$message->author->id}>", false, U::getSomeAkuma($discord));
    }
});

$discord->run();