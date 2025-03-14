<?php

namespace Discord\Proibida;

use Discord\Discord;
use Discord\Parts\Embed\Embed;
use R;

class U
{
    public static function getSomeAkuma(Discord $discord)
    {
        $random = random_int(0, 100);

        if ($random < 60) {
            $embed = new Embed($discord);
            $embed->setTitle('Você achou um... Nada!?');
            $embed->setColor('red');
            $embed->setImage("https://images-ext-1.discordapp.net/external/wEpSctfomfaLtMDp4P026MlymnFVwNtWZ_pINl80L3Q/https/i.pinimg.com/originals/3c/5b/0f/3c5b0fb6c0cc6273e25d164d2dc3f1ca.gif");
            $embed->setFooter('Mais sorte da próxima vez!');
            return $embed;
        } else {
            return self::getAkuma($discord);
        }
    }

    private static function getAkuma(Discord $discord)
    {
        $embed = new Embed($discord);
        $random = random_int(0, 100);
        $akuma = '';

        if ($random < 40) {
            $akuma = self::getByRaridade('Comum');
            $embed->setTitle("Huh... Ok, isso é aceitável, você obteve uma {$akuma->tipo} comum");
            $embed->setColor('#FFC0CB'); 
        } elseif ($random >= 40 && $random < 70) {
            $akuma = self::getByRaridade('Raro');
            $embed->setTitle("Legal! Você conseguiu uma {$akuma->tipo} do tipo raro!");
            $embed->setColor('#FFFF00'); 
        } elseif ($random >= 70 && $random < 88) {
            $akuma = self::getByRaridade('Épico');
            $embed->setTitle("Olha só o que temos aqui... Você conseguiu uma {$akuma->tipo} épica!");
            $embed->setColor('#00FF00'); 
        } elseif ($random >= 88 && $random < 98) {
            $akuma = self::getByRaridade('Lendário');
            $embed->setTitle("Você conseguiu uma {$akuma->tipo} lendária! Incrível!!");
            $embed->setColor('#0000FF'); 
        } else {
            $akuma = self::getByRaridade('Mítico');
            $embed->setTitle("O que!? Você conseguiu uma {$akuma->tipo} mítica?! Onde arranjou isso!?");
            $embed->setColor('#FF0000'); 
        }

        $embed->setImage('https://media1.tenor.com/m/zAwi-9jeOAEAAAAC/akuma-no-mi.gif');
        $embed->setDescription($akuma->description);
        $embed->setFooter($akuma->name);

        return $embed;
    }

    private static function getByRaridade(string $raridade)
    {
        return R::findOne('akuma', " raridade = ? ORDER BY RAND()", [$raridade]);
        
    }

public static function getRandomImage(): string
{
$dois = ['https://media1.tenor.com/m/zAwi-9jeOAEAAAAC/akuma-no-mi.gif', 'https://static.wikia.nocookie.net/onepiece/images/9/92/Devil_Fruit_Infobox.png/revision/latest?cb=20181223211425&path-prefix=pt',
];

return array_rand($dois);
}}