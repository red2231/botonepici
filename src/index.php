<?php

namespace Discord\Proibida;

require_once __DIR__ . '/../vendor/autoload.php';

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
use Discord\Proibida\Entities\Akuma;
use Discord\Proibida\Entities\Usuario;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Dotenv\Dotenv;
use Stichoza\GoogleTranslate\GoogleTranslate;

$dotenv = Dotenv::createMutable(__DIR__ . '/../');
$dotenv->safeLoad();
$token = $_ENV['TOKEN'];

$discord = new Bot([
    'token'   => $token,
    'intents' => [Intents::GUILD_MEMBERS, Intents::AUTO_MODERATION_CONFIGURATION,Intents::AUTO_MODERATION_EXECUTION,Intents::GUILDS, Intents::GUILD_MESSAGES, Intents::MESSAGE_CONTENT, Intents::GUILD_PRESENCES]
]);

$discord->on('init', function () {
    echo 'Bot iniciou' . PHP_EOL;
});
static $processedMessages = [];
$discord->on(Event::MESSAGE_CREATE, function (Message $message, Bot $discord) use (&$processedMessages) {
    static $manager;
    if(!$manager){
        $manager = new AkumaManager($discord->loop);
    }

    $author = $message->author;
    $id = $author->id;
    $url = $author->avatar;
    $is_admin = $message->member->getPermissions()->administrator;
    if (isset($processedMessages[$message->id]) || $message->author->bot) {
        return;
    }
    if (count($processedMessages) >= 600) {
        array_shift($processedMessages);
    }

 
    $manager->cadastrar($id, $url);

    $processedMessages[$message->id] = true;

    $conteudo = $message->content;

    
    if(strcasecmp($conteudo, "+me")===0){
      $manager->GetAkumaByUserId($id)->then(function(?Akuma $akuma) use($discord,$message){
        if($akuma){
            $Embed = (new Embed($discord))
            ->setTitle("A sua akuma: {$akuma->getName()}")
            ->setColor(getColor('purple'));
            $message->reply(MessageBuilder::new()->addEmbed($Embed));
        }else{
            $message->reply("Você não tem akuma no mi");
        }
      });
    }
     

    if(str_starts_with($conteudo, '+set-akuma <@') && $is_admin){
        $targetId = extractId($conteudo);
        $akuma = extractAkuma($conteudo);
        $manager->setAkumaFromAdmin($targetId, $akuma)->then(function(bool $setado) use($message, $targetId, $akuma){
            if($setado ===true){
                $message->reply("O usuário(a) <@{$targetId}> teve a akuma $akuma definida");
                        }else{
                            $message->reply("Usuário ou Akuma não foram achados!");
                        }
                
        });


    }

if(str_starts_with($conteudo, '+rollt <@')){
    
$partes = explode(' ', $conteudo);
$targetid = extractId($conteudo);
$quantidade = extractAmount($conteudo);
$manager->transferRolls($id, $targetid, $quantidade)->then(function(bool $transferiu) use($targetid, $message) {
    if($transferiu ===true){
        $message->reply("Transferência realizada com sucesso para <@{$targetid}>");
        }else{
            $message->reply("Erro na transação! Talvez você não tenha rolls suficientes ou o usuário destino não exista!");
        }
});

}
if (strcasecmp(trim($conteudo), "!akuma") === 0) {
    $manager->hasRoll($id)->then(function (bool $bool) use ($discord, $id, $message, $manager) {
        if ($bool === true) {
            $manager->getSomeAkuma($discord)->then(function (Embed $embed) use ($message, $id, $manager, $discord) {
                $buttonOne = Button::new(Button::STYLE_SUCCESS, "one_{$id}")->setLabel('Aceitar');
                $buttonTwo = Button::new(Button::STYLE_DANGER, "second_{$id}")->setLabel('Recusar');
                $actionRow = ActionRow::new()->addComponent($buttonOne)->addComponent($buttonTwo);

                $builder = MessageBuilder::new()->addEmbed($embed);

                if ($embed->title !== 'Você achou um... Nada!?') {
                    $builder->addComponent($actionRow);
                   
                }
                $message->reply($builder);
                return;
            
            });}
            $manager->getSomeAkuma($discord)->then(function (Embed $embed) use ($message, $id, $manager, $discord) {
                check($id, $manager)->then(function ($value) use($id, $message, $discord, $manager){
                    if ($value === true) {
                        $manager->getSomeAkuma($discord)->then(function (Embed $embed) use ($message, $id) {
                            $buttonOne = Button::new(Button::STYLE_SUCCESS, "one_{$id}")->setLabel('Aceitar');
                            $buttonTwo = Button::new(Button::STYLE_DANGER, "second_{$id}")->setLabel('Recusar');
                            $actionRow = ActionRow::new()->addComponent($buttonOne)->addComponent($buttonTwo);
    
                            $builder = MessageBuilder::new()->addEmbed($embed);
    
                            if ($embed->title !== 'Você achou um... Nada!?') {
                                $builder->addComponent($actionRow);
                            }
    
                            $message->reply($builder);
                        });
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
            });
                });
    });
        }

    if (str_starts_with($conteudo, '+quemesta')) {
        $partes = explode(" ", $conteudo);
        if (count($partes) < 2) {
            $message->reply("Por favor, forneça o nome da akuma!");
            return;
        }
        $akumaName = implode(" ", array_slice($partes, 1));
       $manager->getAkumaUserOrNull($akumaName)->then(function(?Usuario $user)use($discord,$message){
        $Embed = new Embed($discord);
    
        if ($user) {
            $Embed->setColor(getColor('lightskyblue'))
                ->setTitle("A akuma pertence a <@{$user->getUsername()}>")
                ->setImage("{$user->getAvatarUrl()}");
            $message->reply(MessageBuilder::new()->addEmbed($Embed));
        } else {
            $message->reply( "Ei, você está com sorte. Ninguém é detentor dessa akuma no momento ou ela não existe na minha base de dados" );;
        }
    
       });
   
}

    if ((str_starts_with($conteudo, "+add-roll <@" ) || str_starts_with( $conteudo, "+add-roll <@!")))
    {
$partes = explode(' ', $conteudo);
$id = extractId($conteudo);
$quantidade = $partes[2];
if(!is_numeric($quantidade) || !$id || $quantidade<=0 || !$message->member->getPermissions()->administrator){
$message->reply("Quantidade, mensagem inválida ou você não tem permissão de usar esse comando!");
}
else{
 $manager->setAmount($id, $quantidade)->then(function(int $restantes)use($id, $message, $quantidade){
    $message->channel->sendMessage("$quantidade rolls foram entregues a <@{$id}> e agora este usuário possui $restantes rolls restantes!");

 });

}}
if(strcasecmp($conteudo, '+myrolls') ===0){
 $manager->getRollsByUsername($id)->then(fn (int $quantidade)=>$message->reply("Você possui $quantidade rolls restantes"));
}
    });

$discord->on(Event::INTERACTION_CREATE, function (Interaction $interaction, Bot $discord) {
    $akumaManager = new AkumaManager($discord->getLoop());
if($interaction->type=== Interaction::TYPE_MESSAGE_COMPONENT){
$id = $interaction->data->custom_id;
$userId = $interaction->user->id;
$targetId = explode('_', $id)[1];
if( $userId!== $targetId){
$interaction->respondWithMessage("Ei! Essa mensagem não deveria ser respondida por você, engraçadinho", true);
return;
}
$buttonId= explode('_', $id)[0];

    if($buttonId==='one'){
        $akuma  = $interaction->message->embeds[0]->footer->text;
        $interaction->message->delete();
       $akumaManager->associateUser($akuma, $userId);
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
(new AkumaManager($discord->getLoop()))->removeMemberAndGetAkumaName($userId)->then(function(false|string $bool)use($discord, $userId){
    if($bool===false){

        return;
        }else{
            $discord->getChannel('1319160352277008415')
            ->sendMessage("O usuário <@{$userId}> saiu e deixou a akuma $bool livre! Ninguém mandou vazar!");
        }
        
});

});
$discord->on(Event::GUILD_BAN_ADD, function(Ban $ban, Bot $discord){
$userID = $ban->user->id;

 (new AkumaManager($discord->getLoop()))->removeMemberAndGetAkumaName($userID)->then(function(false|string $bool) use($discord, $userID){
    if($bool===false){

        return;
        }else{
            $discord->getChannel('1319160352277008415')
            ->sendMessage("O usuário <@{$userID}> foi banido e deixou a akuma $bool livre! Ninguém mandou desrespeitar as regras!");
        }
 });
});

$discord->run();
