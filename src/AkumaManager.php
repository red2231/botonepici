<?php

namespace Discord\Proibida;
require_once __DIR__.'/teste.php';
use Discord\Discord;
use Discord\Parts\Embed\Embed;
use Discord\Proibida\Entities\Akuma;
use Discord\Proibida\Entities\Usuario;
use Doctrine\ORM\Query\ResultSetMapping;


use function Discord\getColor;

$container = require_once __DIR__.'/utils.php';

class AkumaManager
{
    private $previousAkuma = null;


   
    public  function getSomeAkuma(Discord $discord)
    {
        $random = random_int(0, 100);
     
        if ($random < 50) {
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
        $random = random();
        $akuma = null;

        if ($random < 60) {
            
            $akuma = $this->getByRaridade('Comum');
            $tipo = $akuma->getTipo()->value;
            $embed->setTitle("Huh... Ok, isso é aceitável, você obteve uma $tipo comum");
            $embed->setColor(getColor('blue')); 
        } elseif ($random >= 60 && $random < 85) {
            $akuma = $this->getByRaridade('Raro');
            $tipo = $akuma->getTipo()->value;
            $embed->setTitle("Legal! Você conseguiu uma $tipo do tipo raro!");
            $embed->setColor(getColor('yellow')); 
        } elseif ($random >= 85 && $random < 95) {
            $akuma = $this->getByRaridade('Épico');
            $tipo = $akuma->getTipo()->value;
            $embed->setTitle("Olha só o que temos aqui... Você conseguiu uma $tipo épica!");
            $embed->setColor(getColor('purple')); 
        } elseif ($random >= 95 && $random < 99.9) {
            $akuma = $this->getByRaridade('Lendário');
            $tipo = $akuma->getTipo()->value;
            $embed->setTitle("Você conseguiu uma $tipo lendária! Incrível!!");
            $embed->setColor(getColor('pink')); 
        } else {
            $akuma = $this->getByRaridade('Mítico');
            $tipo = $akuma->getTipo()->value;
            $embed->setTitle("O que!? Você conseguiu uma $tipo mítica?! Onde arranjou isso!?");
            $embed->setColor(getColor('gold')); 
        }

        $embed->setImage('https://media1.tenor.com/m/zAwi-9jeOAEAAAAC/akuma-no-mi.gif');
        $embed->setDescription($akuma->getDescription());
        $embed->setFooter($akuma->getName());
        
        return $embed;
    }
    public function cadastrar(string $userId, string $avatarUrl){
        $EntityManager=$GLOBALS['container']->get('entity');

if(is_null($EntityManager->getRepository(Usuario::class)->findOneBy(['username'=>$userId]))){
    
$user = new Usuario($userId);
$user->setAvatarUrl($avatarUrl);
$EntityManager->persist($user);
$EntityManager->flush();
$EntityManager->getConnection()->close();
}
$EntityManager->getConnection()->close();
}
   private function getByRaridade(string $raridade):Akuma
    {
        $entityManager = $GLOBALS['container']->get('entity');
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
        
        
            $this->previousAkuma = $akuma->getId();
        $entityManager->getConnection()->close();
        
        return $akuma;
    }

    public  function associateUser(string $akuma, string $username)
    {
   $EntityManager = $GLOBALS['container']->get('entity');
   
        $akumaRepo = $EntityManager->getRepository(Akuma::class);
        $user = $EntityManager->getRepository(Usuario::class)->findOneBy(['username'=>$username]);;
        $akum = $akumaRepo->findOneBy(['name' =>$akuma]);
        $user->setAkuma($akum);
        
        $EntityManager->flush();
        $EntityManager->getConnection()->close();

    }
    public function getAkumaUserOrNull(string $name): Usuario|null|false
    {
        $EntityManager = $GLOBALS['container']->get('entity');
        $repo = $EntityManager->getRepository(Akuma::class);
        $reference = $repo->findOneBy(['name' => $name]);
        if(!$reference){
            return false;
        }


        $usuario= $EntityManager->createQueryBuilder()
            ->select('u')
            ->from('Discord\Proibida\Entities\Usuario', 'u')
            ->join('u.akuma', 'a')
            ->where('a.name = :name')
            ->setParameter('name', $name)
            ->getQuery()->getOneOrNullResult();
            $EntityManager->getConnection()->close();
  
  return $usuario;

        
    }

    public function removeMemberAndGetAkumaName(string $username): false|string
    {        $EntityManager = $GLOBALS['container']->get('entity');

        $user = $EntityManager->getRepository(Usuario::class)->findOneBy(['username'=>$username]);
       

        if (!$user) {
            return false;
        }
    
        $akuma = $user->getAkuma();
        $akumaName = $akuma ? $akuma->getName() : null;
    
        try {
            $EntityManager->beginTransaction();
            $EntityManager->remove($user);
            $EntityManager->flush();
            $EntityManager->commit();
            $EntityManager->getConnection()->close();

        } catch (\Exception $e) {
            $EntityManager->rollback();
       

            throw $e; 
        }
    
        return $akumaName ?? false;
    }

    public function getAkumaByUserId(string $userId): ?Akuma
    {
        $EntityManager =$GLOBALS['container']->get('entity');

        $user= $EntityManager->createQueryBuilder()
            ->select('a')
            ->from('Discord\Proibida\Entities\Akuma', 'a')
            ->join('a.user', 'u')
            ->where('u.username = :username')
            ->setParameter('username', $userId)
            ->getQuery()
            ->getOneOrNullResult();

            $EntityManager->getConnection()->close();
            return $user;

    }
    public function hasRoll(string $username):bool
    {
        $EntityManager = $GLOBALS['container']->get('entity');

        $user = $EntityManager->getRepository(Usuario::class)->findOneBy(['username'=>$username]);
        if(!$user || $user->getRolls()<=0){
return false;
        }
        
        $user->setRolls(-1);
        $EntityManager->persist($user);
        $EntityManager->flush();
        $EntityManager->getConnection()->close();

        return true;
    }
    function setAmount(string $username, int $quantidade):int {
        $EntityManager =$GLOBALS['container']->get('entity');

        $user = $EntityManager->getRepository(Usuario::class)->findOneBy(['username'=>$username]);
        $user->setRolls($quantidade);
        $EntityManager->flush();
        $restantes = $user->getRolls();
        $EntityManager->getConnection()->close();

        return $restantes;
    }

    public function hasAkuma(string $username): bool
    {
        $EntityManager = $GLOBALS['container']->get('entity');

        $user = $EntityManager->getRepository(Usuario::class)->findOneBy(['username'=>$username]);
        $EntityManager->getConnection()->close();
        if(!$user || !$user->getAkuma()){
return false;
        }
        return true;
    
    }

    public function getRollsByUsername(string $username):int
    {
        $EntityManager = $GLOBALS['container']->get('entity');

        $builder = $EntityManager->createQueryBuilder();
        $quantidade = $builder
        ->select('u.rolls')
        ->from('Discord\Proibida\Entities\Usuario', 'u')
        ->where('u.username =:username')
        ->setParameter('username', $username);
        
        $quantidade= $quantidade->getQuery()->getSingleScalarResult();
        $EntityManager->getConnection()->close();
        return $quantidade;
    }
    public function transferRolls(string $sourceId, string $targetId, int $amount): bool
    {
        $EntityManager = $GLOBALS['container']->get('entity');

        $EntityManager->beginTransaction(); 
    
        try {
            $user = $EntityManager->getRepository(Usuario::class)->findOneBy(['username'=>$sourceId]);
            
            $user->setRolls( -$amount);
            
            if ($user->getRolls() < 0) {
                $EntityManager->rollback(); 
                return false;
            }
            
    
            $targetUser = $EntityManager->getRepository(Usuario::class)->findOneBy(['username'=>$targetId]);
            
            if(!$targetUser){
                $EntityManager->rollback(); 

return false;
            }
            $targetUser->setRolls($amount);
    
            $EntityManager->flush();
            $EntityManager->commit(); 
            $EntityManager->getConnection()->close();

            return true;
        } catch (\Exception $e) {
            $EntityManager->rollback(); 
            throw $e; 
        }
    }
   


    public function setAkumaFromAdmin(string $targetId, string $akuma)
    {
        $EntityManager = $GLOBALS['container']->get('entity');

        $user = $EntityManager->getRepository(Usuario::class)->findOneBy(['username'=>$targetId]);
        
        $akuma = $EntityManager->getRepository(Akuma::class)->findOneBy(['name' => $akuma]);
        if(!$user || !$akuma){
            return false;
                    }   
                    $user->setAkuma($akuma);
$EntityManager->flush();
$EntityManager->getConnection()->close();
return true;


    }
    }