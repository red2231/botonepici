<?php
namespace Discord\Proibida;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;


function getEntityManager(): EntityManager{
    $connec = DriverManager::getConnection([
        'driver'   => 'mysqli',
        'host'     => 'localhost',
        'port'     => 3306,
        'dbname'   => 'bot',
        'user'     => 'root',
        'password' => 'erick',
        'charset'  => 'utf8mb4'
       ]);
       $config = ORMSetup::createAttributeMetadataConfiguration(
        paths: [__DIR__ . '/Entities'],
        isDevMode: true,
    );
    $EntityManager = new EntityManager($connec, $config);
    return $EntityManager;
}