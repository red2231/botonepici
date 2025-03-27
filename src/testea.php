<?php

use React\Mysql\MysqlClient;
use React\Mysql\MysqlResult;

require_once __DIR__ . '/../vendor/autoload.php';

$cliente = new MysqlClient('root:erick@localhost/bot');

$cliente->query('SELECT count(*) as count from usuario where username=?',['d'])->then(function(MysqlResult $result){
$fields =$result->resultRows;
$valor = (int) $fields[0]['count'];
var_dump($valor);
});