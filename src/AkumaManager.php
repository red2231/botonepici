<?php

namespace Discord\Proibida;
require_once __DIR__ . '/rb-mysql.php';

use Discord\Discord;
use Discord\Parts\Embed\Embed;
use R;

use function Discord\getColor;
use function React\Async\async;
use function React\Async\coroutine;
$dbName    = $_ENV['DB_NAME']??'bot';  
$user      = $_ENV['USER']??'root';
$password  = $_ENV['PASSWORD']??'erick';
$host      = $_ENV['MYSQL_HOST']??'localhost';


if (!R::testConnection()) {
    R::setup("mysql:host=$host;dbname=$dbName", $user, $password);
}

class AkumaManager
{
    private $previousAkuma = null;

    public  function getSomeAkuma(Discord $discord)
    {
        $random = random_int(0, 100);
     
        if ($random < 30) {
            $embed = new Embed($discord);
            $embed->setTitle('Você achou um... Nada!?');
            $embed->setColor(getColor('red'));
            $embed->setImage("https://images-ext-1.discordapp.net/external/wEpSctfomfaLtMDp4P026MlymnFVwNtWZ_pINl80L3Q/https/i.pinimg.com/originals/3c/5b/0f/3c5b0fb6c0cc6273e25d164d2dc3f1ca.gif");
            $embed->setFooter('Mais sorte da próxima vez!');
            return $embed;
        } else {
            return $this->getAkuma($discord);
        }
        
    }

    private function getAkuma(Discord $discord)
    {
        $embed = new Embed($discord);
        $random = random_int(0, 100);
        $akuma = null;

        if ($random < 50) {
            $akuma = $this->getByRaridade('Comum');
            $embed->setTitle("Huh... Ok, isso é aceitável, você obteve uma {$akuma->tipo} comum");
            $embed->setColor(getColor('blue')); 
        } elseif ($random >= 50 && $random < 80) {
            $akuma = $this->getByRaridade('Raro');
            $embed->setTitle("Legal! Você conseguiu uma {$akuma->tipo} do tipo raro!");
            $embed->setColor(getColor('yellow')); 
        } elseif ($random >= 80 && $random < 95) {
            $akuma = $this->getByRaridade('Épico');
            $embed->setTitle("Olha só o que temos aqui... Você conseguiu uma {$akuma->tipo} épica!");
            $embed->setColor(getColor('purple')); 
        } elseif ($random >= 95 && $random < 99) {
            $akuma = $this->getByRaridade('Lendário');
            $embed->setTitle("Você conseguiu uma {$akuma->tipo} lendária! Incrível!!");
            $embed->setColor(getColor('pink')); 
        } else {
            $akuma = $this->getByRaridade('Mítico');
            $embed->setTitle("O que!? Você conseguiu uma {$akuma->tipo} mítica?! Onde arranjou isso!?");
            $embed->setColor(getColor('gold')); 
        }

        $embed->setImage($this->getSomeImage());
        $embed->setDescription($akuma->description);
        $embed->setFooter($akuma->name);
        
        return $embed;
    }

   private function getByRaridade(string $raridade)
    {
        $where = 'raridade = ?';
        $params = [$raridade];
    
        if ($this->previousAkuma !== null) {
            $where .= ' AND id != ?';
            $params[] = $this->previousAkuma->id;
        }
    
        $akuma = R::findOne('akuma', "$where ORDER BY RAND()", $params);
    
        if (!$akuma) {
            $akuma = R::findOne('akuma', 'raridade = ? ORDER BY RAND()', [$raridade]);
        }
    
        $this->previousAkuma = $akuma;
    
        return $akuma;
    }
    public function getSomeImage():string
    {
        $images= ['https://media1.tenor.com/m/i02LN_VG-N8AAAAd/bara-bara-no-mi.gif',
    'https://media1.tenor.com/m/zAwi-9jeOAEAAAAC/akuma-no-mi.gif'];
    return $images[array_rand($images)];
    }
}