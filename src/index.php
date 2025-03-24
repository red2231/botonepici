<?php

namespace Discord\Proibida;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/redis.php';
require_once __DIR__ . '/functions.php';

require_once __DIR__.'/teste.php';
use function Discord\getColor;
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Proibida\AkumaManager;
use function Discord\Proibida\check;
use Discord\Discord as Bot;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Guild\Ban;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\User\Member;
use Discord\Proibida\Entities\Usuario;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Dotenv\Dotenv;
use Exception;
use Stichoza\GoogleTranslate\GoogleTranslate;

$dotenv = Dotenv::createMutable(__DIR__ . '/../');
$dotenv->safeLoad();
$token = $_ENV['TOKEN'];

$discord = new Bot([
    'token'   => $token,
    'intents' => [Intents::GUILD_MEMBERS, Intents::GUILD_MESSAGES, Intents::GUILD_MODERATION, Intents::MESSAGE_CONTENT
    , Intents::DIRECT_MESSAGES, Intents::GUILDS, Intents::GUILD_PRESENCES]
]);

$discord->on('init', function (Bot $discord) {
    echo 'Bot iniciou' . PHP_EOL;
});
$container = require_once __DIR__.'/utils.php';
static $processedMessages = [];
static $limpador = 0;
$discord->on(Event::MESSAGE_CREATE, function (Message $message, Bot $discord) use (&$processedMessages, &$limpador, &$container) {
    $id = $message->author->id;
    $url = $message->author->avatar;
    $limpador++;
 
    if (isset($processedMessages[$message->id]) || $message->author->bot) {
        return;
    }
    (new AkumaManager)->cadastrar($id, $url);
    if (count($processedMessages) >= 200) {
        array_shift($processedMessages);
    }
    $processedMessages[$message->id] = true;

    $conteudo = $message->content;
    if($limpador ==500){
        $EntityManager = $container->get('entity');
        $EntityManager->flush();
        $EntityManager->clear();
        $limpador=0;
    }
    
    if(strcasecmp($conteudo, "+me")===0){
        $akuma = (new AkumaManager)->GetAkumaByUserId($id);
        $author = $message->author;
        if($akuma){
            $Embed = (new Embed($discord))
            ->setTitle("A sua akuma: {$akuma->getName()}")
            ->setColor(getColor('purple'));
            $message->reply(MessageBuilder::new()->addEmbed($Embed));
        }else{
            $message->reply("Você não tem akuma no mi");
        }
        

    }
    if(str_starts_with($conteudo, '+set-akuma <@') && $message->member->getPermissions()->administrator){
        $targetId = extractId($conteudo);
        $akuma = extractAkuma($conteudo);
        $setado = (new AkumaManager)->setAkumaFromAdmin($targetId, $akuma);

        if($setado ===true){
$message->reply("O usuário(a) <@{$targetId}> teve a akuma $akuma definida");
        }else{
            $message->reply("Usuário ou Akuma não foram achados!");
        }

    }



if(str_starts_with($conteudo, '+rollt <@')){
$partes = explode(' ', $conteudo);
$targetid = extractId($conteudo);
$quantidade = extractAmount($conteudo);
$transferiu = (new AkumaManager)->transferRolls($id, $targetid, $quantidade);
if($transferiu ===true){
$message->reply("Transferência realizada com sucesso para <@{$targetid}>");
}else{
    $message->reply("Erro na transação! Talvez você não tenha rolls suficientes ou o usuário destino não exista!");
}
}

    if (strcasecmp(trim($conteudo), "!akuma") === 0) {
        $hasrool = (new AkumaManager)->hasRoll($id);

        if($hasrool ===true){
            $embed = (new AkumaManager)->getSomeAkuma($discord);
            $buttonOne = Button::new(Button::STYLE_SUCCESS, "one_{$id}")->setLabel('Aceitar');
            $buttonTwo = Button::new(Button::STYLE_DANGER, "second_{$id}")->setLabel('Recusar');
            $actionRow = ActionRow::new()
                ->addComponent($buttonOne)
                ->addComponent($buttonTwo);
            $builder = MessageBuilder::new()
                ->addEmbed($embed);
                if($embed->title!=='Você achou um... Nada!?'){
                    $builder->addComponent($actionRow);
                }
                
            $message->reply($builder);
            return;
        }

        $value = check($id);
        if ($value === true) {
            $embed = (new AkumaManager)->getSomeAkuma($discord);
            $buttonOne = Button::new(Button::STYLE_SUCCESS, "one_{$id}")->setLabel('Aceitar');
            $buttonTwo = Button::new(Button::STYLE_DANGER, "second_{$id}")->setLabel('Recusar');
            $actionRow = ActionRow::new()
                ->addComponent($buttonOne)
                ->addComponent($buttonTwo);
            $builder = MessageBuilder::new()
                ->addEmbed($embed);
                if($embed->title!=='Você achou um... Nada!?'){
                    $builder->addComponent($actionRow);
                }
                
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
    



    if (str_starts_with($conteudo, '+quemesta')) {
        $partes = explode(" ", $conteudo);
        if (count($partes) < 2) {
            $message->reply("Por favor, forneça o nome da akuma!");
            return;
        }
        $akumaName = implode(" ", array_slice($partes, 1));
        $user = (new AkumaManager)->getAkumaUserOrNull($akumaName);
        $Embed = new Embed($discord);
    
        if ($user instanceof Usuario) {
            $Embed->setColor(getColor('lightskyblue'))
                ->setTitle("A akuma pertence a <@{$user->getUsername()}>")
                ->setImage("{$user->getAvatarUrl()}");
            $message->reply(MessageBuilder::new()->addEmbed($Embed));
        } else {
            $message->reply($user === null ? "Ei, você está com sorte. Ninguém é detentor dessa akuma no momento" : "Não encontrei nenhuma akuma com esse nome");
        }
    
}
    if (strcasecmp(trim($conteudo), "!spawn") === 0) {
        $value = getRaridaded($id);
        if ($value === true) {
            $raridade = getAnimalRaridade();
            $message->channel->sendMessage("<@{$id}> achou uma criatura de tipo $raridade");
        } else {
            $message->channel->sendMessage("<@{$id}> você só pode enviar essa mensagem uma vez a cada meia hora! Tente novamente em $value");
        }
    }
    if ((str_starts_with($conteudo, "+add-roll <@" ) || str_starts_with( $conteudo, "+add-roll <@!")) && $message->member->getPermissions()->administrator)
    {
$partes = explode(' ', $conteudo);
$id = extractId($conteudo);
$quantidade = $partes[2];
if(!is_numeric($quantidade) || !$id || $quantidade<=0){
$message->reply("Quantidade ou mensagem inválida!");
}
else{
$restantes = (new AkumaManager)->setAmount($id, $quantidade);
$message->channel->sendMessage("$quantidade rolls foram entregues a <@{$id}> e agora este usuário possui $restantes rolls restantes!");
}
}
if(strcasecmp($conteudo, '+myrolls') ===0){
$quantidade = (new AkumaManager)->getRollsByUsername($id);
$message->reply("Você possui $quantidade rolls restantes");
}
});
$discord->on(Event::INTERACTION_CREATE, function (Interaction $interaction){
    $akumaManager = new AkumaManager;
if($interaction->type=== Interaction::TYPE_MESSAGE_COMPONENT){
$id = $interaction->data->custom_id;
$userId = $interaction->user->id;
$imagem= $interaction->user->avatar;
$targetId = explode('_', $id)[1];
if( $userId!== $targetId){
$interaction->respondWithMessage("Ei! Essa mensagem não deveria ser respondida por você, engraçadinho", true);
return;
}
$buttonId= explode('_', $id)[0];

    if($buttonId==='one'){
        $akuma  = $interaction->message->embeds[0]->footer->text;
        $interaction->message->delete();
       $akumaManager->associateUser($akuma, $userId, $imagem);
$interaction->respondWithMessage("A akuma $akuma agora percente a <@{$userId}>! ", false);
}
else{
    $interaction->message->delete();
    $interaction->respondWithMessage("<@{$userId}> Akuma no Mi recusada!", true);
}
}
});
$discord->on(Event::GUILD_MEMBER_REMOVE, function(Member $member, Bot $discord){
$userId = $member->user->id;
$bool = (new AkumaManager)->removeMemberAndGetAkumaName($userId);
if($bool===false){

return;
}else{
    $discord->getChannel('1319160352277008415')
    ->sendMessage("O usuário <@{$userId}> saiu e deixou a akuma $bool livre! Ninguém mandou vazar!");
}

});
$discord->on(Event::GUILD_BAN_ADD, function(Ban $ban, Bot $discord){
$userID = $ban->user->id;

$bool = (new AkumaManager)->removeMemberAndGetAkumaName($userID);
if($bool===false){

return;
}else{
    $discord->getChannel('1319160352277008415')
    ->sendMessage("O usuário <@{$userID}> foi banido e deixou a akuma $bool livre! Ninguém mandou desrespeitar as regras!");
}

});

$discord->run();
