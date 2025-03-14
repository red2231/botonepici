<?php
# PARAMECIAS
namespace Discord\Proihibida;
require_once __DIR__.'/../vendor/autoload.php';
use DI\Container;


$comum1 = 'Beri Beri no Mi, Shari Shari no Mi, Fuku Fuku no Mi, Koro Koro no Mi, Sube Sube no Mi, Iro Iro no Mi, Poke Poke no Mi, Kobu Kobu no Mi, Kilo Kilo no Mi, Guru Guru no Mi, Toge Toge no Mi, Awa Awa no Mi, Ami Ami no Mi, Iro Iro no Mi, Woshu Woshu no Mi, Sui Sui no Mi, Ori Ori no Mi, Nagi Nagi no Mi, Noko Noko no Mi, Mane Mane no Mi, Kuku Kuku no Mi, Jara Jara no Mi';

$raro1 = 'Bata Bata no Mi, Bara Bara no Mi, Bane Bane no Mi, Kama Kama no Mi, Giro Giro no Mi, Hira Hira no Mi, Suke Suke no Mi, Gocha Gocha no Mi, Mini Mini no Mi, Shibo Shibo no Mi, Beta Beta no Mi, Choki Choki no Mi, Doru Doru no Mi, Noro Noro no Mi, Kuri Kuri no Mi, Memo Memo no Mi, Nui Nui no Mi, Mato Mato no Mi, Modo Modo no Mi, Bomu Bomu no Mi, Jake Jake no Mi, Supa Supa no Mi, Goe Goe no Mi, Mosa Mosa no Mi, Sabi Sabi no Mi, Muchi Muchi no Mi, Nomi Nomi no Mi, Hiso Hiso no Mi';

$epico1 = 'Doa Doa no Mi, Buki Buki no Mi, Kachi Kachi no Mi, Pamu Pamu no Mi, Chiyu Chiyu no Mi, Oto Oto no Mi, Buku Buku no Mi, Bisu Bisu no Mi, Gutsu Gutsu no Mi, Oshi Oshi no Mi, Ton Ton no Mi, Kyubu Kyubu no Mi, Ishi Ishi no Mi, Yomi Yomi no Mi, Mira Mira no Mi, Goru Goru no Mi, Hoya Hoya no Mi, Pero Pero no Mi, Shiro Shiro no Mi, Baku Baku no Mi, Gabu Gabu no Mi';

$lendario1 = 'Mero Mero no Mi, Wara Wara no Mi, Ato Ato no Mi, Hana Hana no Mi, Hobi Hobi no Mi, Modo Modo no Mi, Nuke Nuke no Mi, Jiki Jiki no Mi, Maki Maki no Mi, Juku Juku no Mi, Kage Kage no Mi, Peto Peto no Mi, Netsu Netsu no Mi, Mochi Mochi no Mi, Atsu Atsu no Mi, Horo Horo no Mi, Shimo Shimo no Mi, Wapu Wapu no Mi, Gunyo Gunyo no Mi';

$mitico1 = 'Fuwa Fuwa no Mi, Riki Riki no Mi, Shiku Shiku no Mi, Bari Bari no Mi, Gura Gura no Mi, Nikyu Nikyu no Mi, Zushi Zushi no Mi, Doku Doku no Mi, Ope Ope no Mi, Soru Soru no Mi, Ito Ito no Mi, Kira Kira no Mi, Moa Moa no Mi, Gasha Gasha no Mi';

# ZOAN
$comum2 = 'Inu Inu no Mi, Inu Inu no Mi, Inu Inu no Mi, Koara Koara no Mi, Mogu Mogu no Mi, Mushi Mushi no Mi, Mushi Mushi no Mi, Tori Tori no Mi, Uma Uma no Mi, Hito Hito no Mi, Inu Inu no Mi, Uma Uma no Mi, Kame Kame no Mi';

$epico2 = 'Neko Neko no Mi, Neko Neko no Mi, Tori Tori no Mi, Ushi Ushi no Mi, Ushi Ushi no Mi, Sara Sara no Mi, Zou Zou no Mi, Akuma no Mi, Hebi Hebi no Mi, Hebi Hebi no Mi, Inu Inu no Mi, Rhino Rhino no Mi';

$lendario2 = 'Kumo Kumo no Mi, Neko Neko no Mi, Ryu Ryu no Mi, Ryu Ryu no Mi, Ryu Ryu no Mi, Ryu Ryu no Mi, Ryu Ryu no Mi, Ryu Ryu no Mi, Zou Zou no Mi';

$mitico2 = 'Batto Batto no Mi, Hito Hito no Mi, Hito Hito no Mi, Hito Hito no Mi, Hebi Hebi no Mi, Inu Inu no Mi, Inu Inu no Mi, Tori Tori no Mi, Hito Hito no Mi, Uma Uma no Mi, Uo Uo no Mi';

# Logia
# Épico
$epico3 = 'Susu Susu no Mi, Ame Ame no Mi, Pasa Pasa no Mi, Numa Numa no Mi, Yuki Yuki no Mi, Toro Toro no Mi';

$lendario3 = 'Moku Moku no Mi, Mera Mera no Mi, Suna Suna no Mi, Mori Mori no Mi';

$mitico3 = 'Hie Hie no Mi, Magu Magu no Mi, Yami Yami no Mi, Pika Pika no Mi, Goro Goro no Mi, Gasu Gasu no Mi';

$locks = 'Ito Ito no Mi, Nikyuu Nikyuu no Mi, Toshi Toshi no Mi, Ope Ope no Mi, Doku Doku no Mi, Gura Gura no Mi, Fuwa Fuwa no Mi, Zushi Zushi no Mi, Soru Soru no Mi, Mochi Mochi no Mi, Toki Toki no Mi, Nomi Nomi no Mi, Raki Raki no Mi, Gasha Gasha no Mi, Uta Uta no Mi, Yami Yami no Mi, Pika Pika no Mi, Magu, Goro Goro no Mi, Gasu Gasu no Mi, Nika Nika no Mi, Uo Uo no Mi';


$comuns = implode( ', ', [$comum1, $comum2]);
$raros = $raro1;
$epicos = implode(', ', [$epico1, $epico2, $epico3]);
$lendarios = implode(', ', [$lendario1, $lendario2, $lendario3]);
$miticos = implode(', ', [$mitico1, $mitico2, $mitico3]);

$comuns = explode(', ', $comuns);
$raros = explode(', ', $raros);
$epicos = explode(', ', $epicos);
$lendarios = explode(', ', $lendarios);
$miticos = explode(', ', $miticos);
$locks = explode(', ', $locks);

$container = new Container;

$container->set('comum', $comuns);
$container->set('raro', $raros);
$container->set('epico', $epicos);
$container->set('lendario', $lendarios);
$container->set('mitico', $miticos);
$container->set('lock', $locks);
return $container;