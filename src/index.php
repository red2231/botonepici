<?php

namespace Discord\Proibida;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/redis.php';
require_once __DIR__ . '/functions.php';

use function Discord\getColor;
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Proibida\AkumaManager;
use function Discord\Proibida\check;
use Discord\Discord as Bot;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Dotenv\Dotenv;
use Stichoza\GoogleTranslate\GoogleTranslate;

$dotenv = Dotenv::createMutable(__DIR__ . '/../');
$dotenv->safeLoad();
$token = $_ENV['TOKEN'];

$discord = new Bot([
    'token'   => $token,
    'intents' => [Intents::GUILD_MESSAGES, Intents::MESSAGE_CONTENT]
]);

$discord->on('init', function (Bot $discord) {
    echo 'Bot iniciou' . PHP_EOL;
});

$processedMessages = [];

$discord->on(Event::MESSAGE_CREATE, function (Message $message, Bot $discord) use (&$processedMessages) {
    $id = $message->author->id;

    if (isset($processedMessages[$message->id]) || $message->author->bot) {
        return;
    }
    if (count($processedMessages) >= 200) {
        array_shift($processedMessages);
    }
    $processedMessages[$message->id] = true;

    $conteudo = $message->content;

    if (strcasecmp(trim($conteudo), "!akuma") === 0) {
        $value = check($id);
        if ($value === true) {
            $embed = (new AkumaManager)->getSomeAkuma($discord);
            $buttonOne = Button::new(Button::STYLE_SUCCESS, 'one')->setLabel('Aceitar');
            $buttonTwo = Button::new(Button::STYLE_DANGER, 'second')->setLabel('Recusar');
            $actionRow = ActionRow::new()
                ->addComponent($buttonOne)
                ->addComponent($buttonTwo);
            $builder = MessageBuilder::new()
                ->addEmbed($embed)
                ->addComponent($actionRow);
            $message->reply($builder);
        } else {
            $translate = new GoogleTranslate();
            $translate->setTarget('pt-br');

            $embed = new Embed($discord);
            $embed->setTitle("⏳ Limite de uso diário!")
                  ->setDescription("Tente novamente em: " . $translate->translate($value))
                  ->setColor(getColor('darkblue'));
            $builder = MessageBuilder::new()->addEmbed($embed);
            $message->reply($builder);
        }
    }

    if (strcasecmp(trim($conteudo), "!spawn") === 0) {
        $value = getRaridaded($id);
        if ($value === true) {
            $raridade = getAnimalRaridade();
            $message->channel->sendMessage("<@{$id}> achou uma criatura de tipo $raridade");
        } else {
            $message->channel->sendMessage("@{$id}> você só pode enviar essa mensagem uma vez a cada meia hora! Tente novamente em $value");
        }
    }
});

$discord->run();
