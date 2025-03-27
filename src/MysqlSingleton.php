<?php
namespace Discord\Proibida;

use React\EventLoop\LoopInterface;
use React\Mysql\MysqlClient;


  

class MysqlSingleton{
    private static ?MysqlClient $mysql =null;

    public static function getInstance(LoopInterface $loop): MysqlClient
    {
        if(self::$mysql===null){
          self::$mysql=self::getClient($loop);
          return self::$mysql;
        }
        return self::$mysql;
    }
   private static function getClient(LoopInterface $loop){
    
      $host= $_ENV['MYSQL_HOST']??'localhost';
    
      $db=    $_ENV['DB_NAME']??'bot';
      $user= $_ENV['USER']??'root';
    $password=        $_ENV['PASSWORD']??'erick';
    $uri = $user.':'.$password.'@'.$host.'/'.$db;
  return  new MysqlClient(uri: $uri, loop:$loop);
  }
}
  


  
  