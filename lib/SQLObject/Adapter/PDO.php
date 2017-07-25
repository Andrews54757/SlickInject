<?php

namespace \SlickInject\SQLObject\Adapter;

use \SlickInject\SQLObject\SQLInterface as SQLInterface;

class PDO implements SQLInterface {
  private static $con;
  private $defaultDB;
  
  function __construct(){}
  public function connect($dbhost, $dbuser, $dbpass, $dbname){}
  public function close(){}
  public function selectDB($name){}
  public function setDefaultDB(){}
  public function getErrorCode(){}
  public function getError(){}
  public function query($query, $bind_args = NULL){}
  
}
