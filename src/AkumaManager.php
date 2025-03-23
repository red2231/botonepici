<?php

namespace Discord\Proibida;
require_once __DIR__.'/utils.php';
require_once __DIR__.'/teste.php';
use Discord\Discord;
use Discord\Parts\Embed\Embed;
use Discord\Proibida\Entities\Akuma;
use Discord\Proibida\Entities\Usuario;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;


use function Discord\getColor;
use function Discord\Proibida\getEntityManager;


class AkumaManager
{
    private $previousAkuma = null;
    private EntityManager $EntityManager;

public function __construct() {
    $this->EntityManager=getEntityManager();
}
   
    public  function getSomeAkuma(Discord $discord)
    {
        $random = random();
     
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

        $embed->setImage($this->getSomeImage());
        $embed->setDescription($akuma->getDescription());
        $embed->setFooter($akuma->getName());
        
        return $embed;
    }
    public function cadastrar(string $userId, string $avatarUrl){
if(is_null( $this->getUserByUsername($userId))){
$user = new Usuario($userId);
$user->setAvatarUrl($avatarUrl);
$this->EntityManager->persist($user);
$this->EntityManager->flush();
}
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
        
        
            $this->previousAkuma = $akuma->getId();
        
        
        return $akuma;
    }
    public function getSomeImage():string
    {
        $images= ['https://media1.tenor.com/m/i02LN_VG-N8AAAAd/bara-bara-no-mi.gif',
    'https://media1.tenor.com/m/zAwi-9jeOAEAAAAC/akuma-no-mi.gif'];
    return $images[array_rand($images)];
    }
    public  function associateUser(string $akuma, string $username, string $url)
    {
   $EntityManager = getEntityManager();
   
        $akumaRepo = $EntityManager->getRepository(Akuma::class);
        $user = new Usuario($username);
        $akum = $akumaRepo->findOneBy(['name' =>$akuma]);
        $user->setAkuma($akum);
        $user->setAvatarUrl($url);
        $EntityManager->persist($user);
        $EntityManager->flush();
    }
    public function getAkumaUserOrNull(string $name): Usuario|null|false
    {
        $EntityManager = getEntityManager();
        $repo = $EntityManager->getRepository(Akuma::class);
        $reference = $repo->findOneBy(['name' => $name]);
        if(!$reference){
            return false;
        }


        return $EntityManager->createQueryBuilder()
            ->select('u')
            ->from('Discord\Proibida\Entities\Usuario', 'u')
            ->join('u.akuma', 'a')
            ->where('a.name = :name')
            ->setParameter('name', $name)
            ->getQuery()->getOneOrNullResult();
  

        
    }

    public function removeMemberAndGetAkumaName(string $username): false|string
    {
        $user = $this->getUserByUsername($username);
    
        if (!$user) {
            return false;
        }
    
        $akuma = $user->getAkuma();
        $akumaName = $akuma ? $akuma->getName() : null;
    
        try {
            $this->EntityManager->beginTransaction();
            $this->EntityManager->remove($user);
            $this->EntityManager->flush();
            $this->EntityManager->commit();
        } catch (\Exception $e) {
            $this->EntityManager->rollback();
            throw $e; 
        }
    
        return $akumaName ?? false;
    }

    public function getAkumaByUserId(string $userId): ?Akuma
    {
        return $this->EntityManager->createQueryBuilder()
            ->select('a')
            ->from('Discord\Proibida\Entities\Akuma', 'a')
            ->join('a.user', 'u')
            ->where('u.username = :username')
            ->setParameter('username', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function hasRoll(string $username):bool
    {
        $user = $this->getUserByUsername($username);
        if(!$user || $user->getRolls()<=0){
return false;
        }
        
        $user->setRolls(-1);
        $this->EntityManager->persist($user);
        $this->EntityManager->flush();
        return true;
    }
    function setAmount(string $username, int $quantidade):int {
        $user = $this->getUserByUsername($username);
        $user->setRolls($quantidade);
        $this->EntityManager->persist($user);
        $this->EntityManager->flush();
        $restantes = $user->getRolls();
        return $restantes;
    }

    public function hasAkuma(string $username): bool
    {
       
        $user = $this->getUserByUsername($username);
        return is_null($user) || is_null( $user->getAkuma());
    }

    public function getRollsByUsername(string $username):int
    {
        $builder = $this->EntityManager->createQueryBuilder();
        $quantidade = $builder
        ->select('u.rolls')
        ->from('Discord\Proibida\Entities\Usuario', 'u')
        ->where('u.username =:username')
        ->setParameter('username', $username);
        return (int) $quantidade->getQuery()->getSingleScalarResult();
    }
    public function transferRolls(string $sourceId, string $targetId, int $amount): bool
    {
        $this->EntityManager->beginTransaction(); 
    
        try {
            $user = $this->getUserByUsername($sourceId);
            
            $user->setRolls( -$amount);
            
            if ($user->getRolls() < 0) {
                $this->EntityManager->rollback(); 
                return false;
            }
            
            $this->EntityManager->persist($user);
    
            $targetUser = $this->getUserByUsername($targetId);
            
            if(!$targetUser){
                $this->EntityManager->rollback(); 

return false;
            }
            $targetUser->setRolls($amount);
    
            $this->EntityManager->persist($targetUser);
            $this->EntityManager->flush();
            $this->EntityManager->commit(); 
            return true;
        } catch (\Exception $e) {
            $this->EntityManager->rollback(); 
            throw $e; 
        }
    }
    public function getUserByUsername(string $username):?Usuario
    {
        return $this->EntityManager->getRepository(Usuario::class)->findOneBy(['username' => $username]);
    }

    }