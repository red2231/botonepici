<?php

namespace Discord\Proibida;
require_once __DIR__.'/teste.php';

use Aura\SqlQuery\Common\QuoterInterface;
use Aura\SqlQuery\Mysql\Quoter;
use Aura\SqlQuery\Mysql\Select;
use Aura\SqlQuery\QueryFactory;
use Discord\Discord;
use Discord\Parts\Embed\Embed;
use Discord\Proibida\Entities\Akuma;
use Discord\Proibida\Entities\Usuario;
use Laminas\Hydrator\ClassMethodsHydrator;
use React\EventLoop\LoopInterface;
use React\Mysql\MysqlResult;
use React\Promise\Promise;
use React\Promise\PromiseInterface;

use function Discord\getColor;
use function React\Promise\resolve;

class AkumaManager
{
    private $previousAkuma = null;
    private LoopInterface $loop;
    private QueryFactory $factory;
public function __construct(LoopInterface $loop)
{
    $this->loop=$loop;
    $this->factory=  new QueryFactory('mysql');
}
   
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
        $cliente = MysqlSingleton::getInstance($this->loop);
        $cliente->query('INSERT IGNORE INTO usuario(username,avatarUrl,rolls ) values (?, ?, ?)', [$userId, $avatarUrl, 1])
        ->catch(function(\Throwable $ex){
$ex->getMessage();
return null;
        });

}
   private function getByRaridade(string $raridade):Akuma
    {
        $cliente = MysqlSingleton::getInstance($this->loop);
        
        $hydrator = new ClassMethodsHydrator;
        $akuma = new Akuma;
              if ($this->previousAkuma !== null) {
            $sql = "
                SELECT * FROM akuma WHERE raridade = ? AND usuario_id IS NULL AND id != ? ORDER BY RAND() LIMIT 1";
                $cliente->query($sql, [$raridade, $this->previousAkuma])->then(function(MysqlResult $result) use ( $hydrator, &$akuma){
                    $results = $result->resultRows[0];
                   
                  $akuma=  $hydrator->hydrate($results, $akuma);
                });
        } else {
            $sql = "
                SELECT *
                FROM akuma
                WHERE raridade = :raridade
                AND usuario_id IS NULL
                ORDER BY RAND()
                LIMIT 1";
           $cliente->query($sql, [$raridade])->then(function(MysqlResult $result) use ($hydrator, &$akuma){
            $results = $result->resultRows[0];
        
         $akuma=   $hydrator->hydrate($results, $akuma);
           });
        }
        
        
            $this->previousAkuma = $akuma->getId();
      
        return $akuma;
    }

    public  function associateUser(string $akuma, string $username)
    {
        
        $cliente = MysqlSingleton::getInstance($this->loop);
        $sql = 'UPDATE usuario 
        SET akuma_id = (SELECT id FROM akuma WHERE name = ? LIMIT 1)
        WHERE username = ?';
        $cliente->query($sql, [$akuma, $username])->then(function(MysqlResult $result){
            $rows = $result->affectedRows;
            echo "Linhas afetadas: $rows";
        });
 

    }
    public function getAkumaUserOrNull(string $name):PromiseInterface
    {

        $user = new Usuario;
       $one = 'SELECT count(*) as count FROM akuma WHERE name = ?';
       $cliente = MysqlSingleton::getInstance($this->loop);
      return  $cliente->query($one, [$name])->then(function(MysqlResult $result){
            if($result[0]['count']===0){
                return false;
            }
        });

            $second= 'SELECT u.* FROM usuario u JOIN akuma a ON u.akuma_id = a.id WHERE a.name = ? LIMIT 1';
$cliente->query($second, [$name])
->then(function(MysqlResult $result) use($user){

$hydrator =new ClassMethodsHydrator;
$user = $hydrator->hydrate($result->resultRows, $user);
return $user;
});


    }

    public function removeMemberAndGetAkumaName(string $username): PromiseInterface
    {      
          $cliente =MysqlSingleton::getInstance($this->loop);
        $exists = 'SELECT COUNT(*) as count from usuario where username =?';


 
return $cliente->query($exists, [$username])->then(function(MysqlResult $result) use($username, $cliente) {
    if($result[0]['count']===0){
    return resolve(false);
    }


 

    $getName = 'SELECT a.name as nome from Akuma INNER JOIN usuario ON a.usuario_id = usuario.id where usuario.username =? LIMIT 1';
    $cliente->query($getName, [$username])
    ->then(function(MysqlResult $result) use ($username, $cliente){
        $rows = $result->resultRows;
       $nome = $rows[0]['nome'];
    $select= 'DELETE FROM usuario where username = ? ';
    $cliente->query($select, [$username]);
    return resolve($nome);
    });
});


    
       
    }



        
    public function getAkumaByUserId(string $userId): PromiseInterface
    {
        $cliente = MysqlSingleton::getInstance($this->loop);
        $query = new QueryFactory('mysql');
        $select= $query->newSelect();
     $statement=   $select->cols(['a.*'])
        ->from('Akuma a')
        ->join('inner', 'usuario as u', 'a.usuario_id=u.id' )
        ->where('u.username = :username')
        ->bindValue('username', $userId)->limit(1)->getStatement();
       return $cliente->query($statement)
        ->then(function(MysqlResult $result){
            $rows = $result->resultRows[0];
            $hydrator = new ClassMethodsHydrator;
            $Akuma = new Akuma;
         $Akuma=   $hydrator->hydrate($rows, $Akuma);
return $Akuma;
        });
        
      


    }
    public function hasRoll(string $username):PromiseInterface
    {

        $cliente = MysqlSingleton::getInstance($this->loop);
        $select = $this->factory->newSelect();
        $sql = $select->cols(['rolls' =>'roll'])
        ->from('usuario')
        ->where('username =?')
        ->limit(1)->getStatement();
    return    $cliente->query($sql, [$username])
        ->then(function(MysqlResult $result) use($cliente, $username){
            $roll =(int) $result->resultRows[0]['roll'];
            if($roll<=0){
return resolve(false);
            }
            $roll-=1;
            $update = $this->factory->newUpdate();
            $sql = $update
            ->table('usuario')
            ->set('rolls', $roll)
            ->where('username=?');
            $cliente->query($sql, [$username]);
            return resolve(true);
        });
        
    }
    function setAmount(string $username, int $quantidade):PromiseInterface {
        $cliente = MysqlSingleton::getInstance($this->loop);


        $sql= 'UPDATE usuario SET rolls = rolls + ? where username = ?';
        $cliente->query($sql, [$quantidade, $username]);
        $select = 'SELECT rolls as roll from usuario where username=? limit 1';
      return  $cliente->query($select, [$username])
        ->then(function(MysqlResult $result) {
            return $result->resultRows[0]['roll'];
        });
        
    }

    public function hasAkuma(string $username):PromiseInterface
    {
$cliente = MysqlSingleton::getInstance($this->loop);
$sql = $this->factory->newSelect()->cols(['u.*'])
->from('usuario u')
->join('inner', 'akuma a', 'u.id = a.usuario_id')
->where('u.username =?')->getStatement();
return $cliente->query($sql, [$username])
->then(function (MysqlResult $result){
if($result===null){
return resolve(false);
}else{
    return resolve(true);
}
});
    
    }

    public function getRollsByUsername(string $username):PromiseInterface
    {
        $cliente = MysqlSingleton::getInstance($this->loop);

        $builder = $this->factory->newSelect()->cols(['rolls' => 'roll'])
        ->from('usuario')
        ->where('username=?')->limit(1)->getStatement();
        return $cliente->query($builder, [$username])
        ->then(function(MysqlResult $result){
            return $result[0]['roll'];
        });
    }
    public function transferRolls(string $sourceId, string $targetId, int $amount){
//     {
//         $EntityManager = $GLOBALS['container']->get('entity');

//         $EntityManager->beginTransaction(); 
    
//         try {
//             $user = $EntityManager->getRepository(Usuario::class)->findOneBy(['username'=>$sourceId]);
            
//             $user->setRolls( -$amount);
            
//             if ($user->getRolls() < 0) {
//                 $EntityManager->rollback(); 
//                 return false;
//             }
            
    
//             $targetUser = $EntityManager->getRepository(Usuario::class)->findOneBy(['username'=>$targetId]);
            
//             if(!$targetUser){
//                 $EntityManager->rollback(); 

// return false;
//             }
//             $targetUser->setRolls($amount);
    
//             $EntityManager->flush();
//             $EntityManager->commit(); 

//             return true;
//         } catch (\Exception $e) {
//             $EntityManager->rollback(); 
//             throw $e; 
//         }
    }
   


    public function setAkumaFromAdmin(string $targetId, string $akuma)
    {
//         $EntityManager = $GLOBALS['container']->get('entity');

//         $user = $EntityManager->getRepository(Usuario::class)->findOneBy(['username'=>$targetId]);
        
//         $akuma = $EntityManager->getRepository(Akuma::class)->findOneBy(['name' => $akuma]);
//         if(!$user || !$akuma){
//             return false;
//                     }   
//                     $user->setAkuma($akuma);
// $EntityManager->flush();
// return true;


//     }
    }}