<?php

namespace Discord\Proibida;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/redis.php';

use function Discord\getColor;

use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Proibida\AkumaManager;
use function Discord\Proibida\check;
use Discord\Discord as Bot;
use Discord\Helpers\Collection;

use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\WebSockets\MessageInteraction;
use Discord\Parts\WebSockets\MessageReaction;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Dotenv\Dotenv;
use Stichoza\GoogleTranslate\GoogleTranslate;

$dotenv = Dotenv::createMutable(__DIR__ . '/../');
$dotenv->safeLoad();

    $token     = $_ENV['TOKEN'];
    $dbName    = $_ENV['DB_NAME']??'bot';  
    $user      = $_ENV['USER']??'root';
    $password  = $_ENV['PASSWORD']??'erick';
    $host      = $_ENV['MYSQL_HOST']??'localhost';


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
    
    if (isset($processedMessages[$message->id]) || $message->author->bot) {
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
            $embed = (new AkumaManager)->getSomeAkuma($discord);
            $buttonone = Button::new(Button::STYLE_SUCCESS, 'one')->setLabel('Aceitar');
            $buttontwo = Button::new(Button::STYLE_DANGER, 'second') ->setLabel('Recusar');
            $ac = ActionRow::new()->addComponent($buttonone)
            ->addComponent($buttontwo);
            $en = MessageBuilder::new()->addEmbed($embed)
            ->addComponent($ac);
            $message->reply($en);
        } else {
            $translate = new GoogleTranslate;
            $translate->setTarget('pt-br');
            
            $embed = new Embed($discord);
            
            $embed->setTitle("⏳ Limite de uso diário!") -> setDescription("Tente novamente em: " . $translate->translate($value))
           ->setColor(getColor('darkblue')) ;
            $mess = MessageBuilder::new()
            ->addEmbed($embed);
        
            $message->reply($mess);
           
                    }
    }});

$discord->run();
