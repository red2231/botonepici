<?php

use Discord\Proibida\Entities\Akuma;
use Discord\Proibida\Entities\AkumaToAdd;
use Discord\Proibida\Entities\Usuario;
require_once __DIR__.'/utils.php';
use function Discord\Proibida\getEntityManager;

function frontHandler(string $akumaName,string $userId, string $url ){
$EntityManager = getEntityManager();
$repo = $EntityManager->getRepository(Akuma::class);
$query= $repo->createQueryBuilder('u')
->where(['u.name =:name'])
->setParameter('name', $akumaName);
$akuma =$query->getQuery()->getOneOrNullResult();
if(!$akuma){
$toAdd = new AkumaToAdd;
$toAdd->setName($akumaName);
$EntityManager->persist($toAdd);
$EntityManager->flush();
return false;
}else{
$user = new Usuario($userId);
$user->setAvatarUrl($url);
$user->setAkuma($akuma);
$EntityManager->persist($user);
$EntityManager->flush();
return true;
}}