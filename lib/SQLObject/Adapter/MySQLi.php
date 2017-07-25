<?php

namespace \SlickInject\SQLObject\Adapter;

use \SlickInject\SQLObject\SQLInterface as SQLInterface;

class MySQLi implements SQLInterface{
  
  private static $con;
  private $defaultDB;
  function __construct()
  {
    if(count(func_get_args()) === 4) 
    { return call_user_func_args(array($this, "connect"), func_get_args()); }
  }
  
  public function connect($dbhost, $dbuser, $dbpass, $dbname)
  {
    if(isset(self::$con)) return;
    self::$con = call_user_func_args("mysqli_connect", func_get_args());
    $this->defaultDB = $dbname;
  }
  
  public function close()
  {
    mysqli_close(self::$con);
  }
  
  public function selectDB($name)
  {
    mysqli_select_db($name);
  }
  
  public function setDefaultDB()
  {
    mysqli_select_db($this->defaultDB);
  }
  
  public function getErrorCode()
  {
    return @\mysqli_connect_error;
  }
  
  public function getError()
  {
    return @\mysqli_error(self::$con);
  }
  
  public function query($query, $bind_args = NULL)
  {
    try{
      // have may have to reformat the queries, and bind properties to fit your adapter
      $prep = self::$con->stmt_init();
      if($prep->prepare($query)) : 
        if(isset($bind_args) && $bind_args != NULL) :
          call_user_func_args(array($prep, "bind_param"), $bind_args); endif;
        return $prep; endif;
    }catch(\Exception $ex) 
    { return false; }
    return false;
  }
  
}
