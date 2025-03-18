<?php
namespace Discord\Proibida;
require_once __DIR__ . '/rb-mysql.php';

use R;

$dbName    = $_ENV['DB_NAME']??'bot';  
$user      = $_ENV['USER']??'root';
$password  = $_ENV['PASSWORD']??'erick';
$host      = $_ENV['MYSQL_HOST']??'localhost';


if (!R::testConnection()) {
    R::setup("mysql:host=$host;dbname=$dbName", $user, $password);
}
$user = R::dispense('usuario');
$user->name='erick';
$user->email='erickverissimodasilva@gmail';
$idu=R::store($user);
$perfil = R::dispense('profile');
$perfil->foto='ugue';
$perfil->user=$user;
R::store($perfil);

echo 'salvo';