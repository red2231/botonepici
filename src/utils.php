<?php
namespace Discord\Proibida;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;




function getEntityManager(): EntityManager{
    $connec = DriverManager::getConnection([
        'driver'   => 'pdo_mysql',
        'host'     => $_ENV['MYSQL_HOST']??'localhost',
        'port'     => 3306,
        'dbname'   =>  $_ENV['DB_NAME']??'bot',
        'user'     => $_ENV['USER']??'root',
        'password' => $_ENV['PASSWORD']??'erick',
        'charset'  => 'utf8mb4'
       ]);
       $config = ORMSetup::createAttributeMetadataConfiguration(
        paths: [__DIR__ . '/Entities'],
        isDevMode: true,
    );
    $EntityManager = new EntityManager($connec, $config);
    return $EntityManager;
}


function extractUserIdFromRaw(string $raw){

}