<?php
namespace Discord\Proibida;

require_once __DIR__ . '/../vendor/autoload.php';

use Aura\SqlQuery\QueryFactory;

  $query = new QueryFactory('mysql');
  $select= $query->newSelect();
echo $select->cols(['a.*'])
->from('Akuma a')
->join('inner', 'usuario as u', 'a.usuario_id=u.id' )
->where('u.username = :username')
->bindValue('username', 'erick')->limit(1) ->getStatement();