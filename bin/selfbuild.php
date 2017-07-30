<?php
// running @ http://legitsoulja.info/SlickInject.php
include "PHP/Beautifier.php"; // http://pear.php.net/package/PHP_Beautifier/doc

$default = "America/Eastern";
$clean = array("<?php","<?","?>");

$o = (object) json_decode(file_get_contents('http://ip-api.com/json/' . (isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'])), true);
date_default_timezone_set(((isset($o->timezone))?$o->timezone:$default));

unset($o, $default);
function scopePos($content){
  $r = explode("#buildbelow", $content);
  $r = str_replace("#endbuild", "", $r[1]);
  return $r;
}

$sqo = scopePos(file_get_contents("https://raw.githubusercontent.com/LegitSoulja/SlickInject/master/lib/SQLObject/SQLObject.php?c=".time()/2));
$si = scopePos(file_get_contents("https://raw.githubusercontent.com/LegitSoulja/SlickInject/master/lib/SlickInject/SlickInject.php?c=".time()/2));
$parser = scopePos(file_get_contents("https://raw.githubusercontent.com/LegitSoulja/SlickInject/master/lib/SlickInject/Parser.php?c=".time()/2));
$d = date('l jS \of F Y h:i:s A');
header("Content-Type: application/octet-stream");
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"SlickInject.php\"");

$build = <<<BUILD
<?php 
/*
\| Compiled & Built on {$d} : Clean & Formatted
\| SlickInject v2
\| @Author: LegitSoulja
\| @License: MIT
\| @Source: https://github.com/LegitSoulja/SlickInject
*/
namespace {
  if (!extension_loaded('mysqlnd')) throw new Error("Failed to load nd_mysqli extension.");
  use SlickInject\Parser as Parser;
  use SlickInject\SQLObject as SQLObject;
  define("SI_VERSION", 102); // 1.0.2
  
  {$si}
}

namespace SlickInject {
  {$sqo}
  {$parser}
}
BUILD;

$pb = new PHP_Beautifier();
$pb->addFilter("ArrayNested");
//$pb->addFilter("ListClassFunction");
$pb->addFilter('EqualsAlign');
$pb->addFilter('NewLines');
$pb->addFilter('IndentStyles');
$pb->setInputString($build);
unset($si, $sqo, $parser);
$pb->process();
$h= $pb->get();
//$h.="•••"; // cause an error
$h = str_replace($clean, "", $h);
try{
    eval($h);
    unset($h);
    $pb->show();
    die();
}catch(Error $ex){
    //print_r($ex);
    header("Content-Type: text/html");
    $expl = explode(PHP_EOL, $h);
    $line = $ex->getLine() ;
    $stack = $expl[$line - 1];
    unset($expl);
    die("Failed to parse : ".$ex->getMessage()."<br/><br/> : Line ".$line."<br/><br/>: Stack -> ".$stack);
}
die();

