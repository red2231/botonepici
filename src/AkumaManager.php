<?php

namespace Discord\Proibida;
require_once __DIR__.'/utils.php';
use Discord\Discord;
use Discord\Parts\Embed\Embed;
use Discord\Proibida\Entities\Akuma;
use Discord\Proibida\Entities\Usuario;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use R;

use function Discord\getColor;
use function Discord\Proibida\getEntityManager;
$dbName    = $_ENV['DB_NAME']??'bot';  
$user      = $_ENV['USER']??'root';
$password  = $_ENV['PASSWORD']??'erick';
$host      = $_ENV['MYSQL_HOST']??'localhost';

class AkumaManager
{
    private $previousAkuma = null;
    public  function getSomeAkuma(Discord $discord)
    {
        $random = random_int(0, 100);
     
        if ($random < 20) {
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
            $tipo = $akuma->tipo->value;
            $embed->setTitle("Huh... Ok, isso é aceitável, você obteve uma $tipo comum");
            $embed->setColor(getColor('blue')); 
        } elseif ($random >= 50 && $random < 80) {
            $akuma = $this->getByRaridade('Raro');
            $tipo = $akuma->tipo->value;
            $embed->setTitle("Legal! Você conseguiu uma $tipo do tipo raro!");
            $embed->setColor(getColor('yellow')); 
        } elseif ($random >= 80 && $random < 95) {
            $akuma = $this->getByRaridade('Épico');
            $tipo = $akuma->tipo->value;
            $embed->setTitle("Olha só o que temos aqui... Você conseguiu uma $tipo épica!");
            $embed->setColor(getColor('purple')); 
        } elseif ($random >= 95 && $random < 99) {
            $akuma = $this->getByRaridade('Lendário');
            $tipo = $akuma->tipo->value;
            $embed->setTitle("Você conseguiu uma $tipo lendária! Incrível!!");
            $embed->setColor(getColor('pink')); 
        } else {
            $akuma = $this->getByRaridade('Mítico');
            $tipo = $akuma->tipo->value;
            $embed->setTitle("O que!? Você conseguiu uma $tipo mítica?! Onde arranjou isso!?");
            $embed->setColor(getColor('gold')); 
        }

        $embed->setImage($this->getSomeImage());
        $embed->setDescription($akuma->description);
        $embed->setFooter($akuma->name);
        
        return $embed;
    }

   private function getByRaridade(string $raridade):Akuma
    {
        $entityManager = getEntityManager();
        $akuma = null;
        $rsm = new ResultSetMapping();

        $rsm->addEntityResult(Akuma::class, 'u');
        
        $rsm->addFieldResult('u', 'id', 'id');
        $rsm->addFieldResult('u', 'name', 'name');
        
        $rsm->addFieldResult('u', 'raridade', 'raridade');
        $rsm->addFieldResult('u', 'tipo', 'tipo');
        $rsm->addFieldResult('u', 'description', 'description');  
              if ($this->previousAkuma !== null) {
            $sql = "
                SELECT *
                FROM akuma
                WHERE raridade = :raridade
                  AND usuario_id IS NULL
                  AND id != :id
                ORDER BY RAND()
                LIMIT 1";
            $query = $entityManager->createNativeQuery($sql,$rsm);
            $query->setParameter('raridade', $raridade);
            $query->setParameter('id', $this->previousAkuma);
            
            $akuma = $query->getOneOrNullResult();
        } else {
            $sql = "
                SELECT *
                FROM akuma
                WHERE raridade = :raridade
                AND usuario_id IS NULL
                ORDER BY RAND()
                LIMIT 1";
            $query = $entityManager->createNativeQuery($sql,$rsm);
            $query->setParameter('raridade', $raridade);
            $akuma = $query->getOneOrNullResult();
        }
        
        
            $this->previousAkuma = $akuma->id;
        
        
        return $akuma;
    }
    public function getSomeImage():string
    {
        $images= ['https://media1.tenor.com/m/i02LN_VG-N8AAAAd/bara-bara-no-mi.gif',
    'https://media1.tenor.com/m/zAwi-9jeOAEAAAAC/akuma-no-mi.gif'];
    return $images[array_rand($images)];
    }
    public  function associateUser(string $akuma, string $username)
    {
   $EntityManager = getEntityManager();
        $userRepo = $EntityManager->getRepository(Usuario::class);
        $akumaRepo = $EntityManager->getRepository(Akuma::class);
        $user = new Usuario($username);
        $akum = $akumaRepo->findOneBy(['name' =>$akuma]);
        $user->setAkuma($akum);
        $EntityManager->persist($user);
        $EntityManager->flush();
    }


}
