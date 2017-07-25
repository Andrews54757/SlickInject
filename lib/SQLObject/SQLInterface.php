<?php

namespace SlickInject\SQLObject;

interface SQLInterface {
  
  public function query($query, $bind_args); // bind_args are the values that are being binded to query after prepared.
  
  public function connect($dbhost, $dbuser, $dbpass, $dbname); // connect to database
  
  public function close(); // close current database
  
  public function getErrorCode(); // return past error code, if error was ever present
  
  public function getError(); // return error details if query fails.
  
  public function selectDB($name);
  
  public function setDefaultDB(); // set database to it's default.
  
}

