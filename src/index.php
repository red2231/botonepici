<?php

namespace Discord\Proibida;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/rb-mysql.php';
require_once __DIR__ . '/U.php';
require_once __DIR__.'/teste.php';
use function Discord\getColor;
use function Discord\Proibida\addUserTimes;
use function Discord\Proibida\check;
use Discord\Discord as Bot;
use R;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Dotenv\Dotenv;
use Stichoza\GoogleTranslate\GoogleTranslate;


$dotenv = Dotenv::createMutable(__DIR__ . '/../');
$dotenv->safeLoad();

$token     = $_ENV['TOKEN'];
$dbName    = $_ENV['DB_NAME'];  
$user      = $_ENV['USER'];
$password  = $_ENV['PASSWORD'];
$host      = 'mysql.railway.internal';

if (!R::testConnection()) {
    R::setup("mysql:host=$host;dbname=$dbName", $user, $password);
}

$discord = new Bot([
    'token'   => $token,
    'intents' => [Intents::GUILD_MESSAGES, Intents::MESSAGE_CONTENT]
]);

$discord->on('init', function (Bot $discord) {
    echo 'Bot iniciou' . PHP_EOL;
});

 $processedMessages = []; 

$discord->on(Event::MESSAGE_CREATE, function (Message $message, bot $discord) use (&$processedMessages) {
    $id = $message->author->id;

    if ($message->author->bot) {
        return;
    }
 
    if (isset($processedMessages[$message->id])) {
        return;
    }
    if (count($processedMessages) >= 100) {
        array_shift($processedMessages);
    }
    $processedMessages[$message->id] = true;

    $conteudo = $message->content;
 

    if (strcasecmp(trim($conteudo), "!akuma") === 0) {
        $value = check($id);
        if ($value === true) {
            $message->channel->sendMessage(
                "<@{$message->author->id}>", 
                false, 
                U::getSomeAkuma($discord)
            );
        } else {
            $translate = new GoogleTranslate();
            $translate->setSource('en');
            $translate->setTarget('pt-br');
            
            $embed = new Embed($discord);
            $embed->setTitle("⏳ Limite de uso diário!");
            $embed->setDescription("Tente novamente em: " . $translate->translate($value));
            $embed->setColor(getColor('darkblue'));
            
            $message->channel->sendMessage(
                "<@{$message->author->id}>", false, $embed);
        }
    }});

$discord->run();
