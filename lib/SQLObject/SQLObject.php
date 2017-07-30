<?php

/*
\| @Author: LegitSoulja
\| @License: MIT
\| @Source: https://github.com/LegitSoulja/SlickInject
*/

namespace SlickInject;

#buildbelow
class SQLResponce
{
    
    private $result;
    private $rows;
    private $row;
    private $stmt;
    
    /**
     * SQLResponce constructor
     * @return void
     */
    function __construct($stmt, $rows = array())
    {
        $this->result = $stmt->get_result();
        $this->stmt   = $stmt;
        if ($this->result->num_rows < 1) return;
        while ($row = $this->result->fetch_assoc()) 
        { array_push($rows, $row); }
     
        if (count($rows) === 1) return $this->row = $rows;
        return $this->rows = $rows;
    }
    
    /**
     * Return mysqli_result object
     * @return object
     */
    public function getResult()
    { return $this->result; }
    
    /**
     * Check if any rows was affected during execution
     * @return bool
     */
    public function didAffect()
    { return ($this->stmt->affected_rows > 0) ? true : false; }
    
    /**
     * Check if result hasn't failed
     * @return bool
     */
    public function error()
    { return ($this->result) ? true : false; }
    
    /**
     * Check if results contain rows
     * @return bool
     */
    public function hasRows()
    { return ((count($this->rows) > 0) || (count($this->row) > 0)) ? true : false; }
    
    /**
     * Return number of rows
     * @return int
     */
    public function num_rows()
    { return (int) $this->result->num_rows; }
    
    /**
     * Return rows from results
     * @return array
     */
    public function getData()
    { 
     // echo count(self::$rows);
      return (count($this->rows) > 0) ? $this->rows : $this->row; 
    
    }
}

class SQLObject
{
    private static $con;
    private $d_db_name; // default database name
    private $isInDefault = true;
    
    /**
     * SQLObject constructor | Can accept database  credentials
     * @return void
     */
    function __construct()
    {
        $args = func_get_args();
        if (count($args) === 4)
            return $this->connect($args[0], $args[1], $args[2], $args[3]);
    }
    
    
    /**
     * Close database connected
     * @return void
     */
    public function close()
    { @\mysqli_close(self::$con); }
    
    
    /**
     * Connect to database
     * @param string $db_host          Database host
     * @param string $db_user          Database username
     * @param string $db_pass          Database password
     * @param string $db_name          Database name
     * @return void
     */
    public function connect($db_host, $db_user, $db_pass, $db_name)
    {
        if ($this->isConnected()) return;
        $this->d_db_name = $db_name;
        self::$con = new \mysqli($db_host, $db_user, $db_pass, $db_name);
    }
    
    /**
     * [Private] Checks if the database connection was ever established.
     * @return bool
     */
    private function isConnected()
    { return (isset(self::$con) && $this->ping()) ? true : false; }
    
    public function select_db($name){
        $this->isInDefault = false;
        return mysqli_select_db(self::$con, $name);
    }
    
    /**
     * Get connect error status
     * @return int
     */
    public function getConnectionError()
    { return @\mysqli_connect_error(); }
    
    /**
     * Get last error from a failed prepare, and or execute.
     * @return string
     */
    public function getLastError()
    { return @\mysqli_error(self::$con); }
    
    /** Deprecated [Useless]
     * Escape string using mysqli
     * @return string
     */
    private function escapeString(&$string)
    { return self::$con->real_escape_string($string); }
    
    /**
     * Check if connection still live
     * @return bool
     */
    public function ping()
    { return (@self::$con->ping()) ? true : false; }
    
    private function set_default_db()
    { 
        $this->isInDefault = true;
        return mysqli_select_db(self::$con, $this->d_db_name); 
    }
    
    /**
     * Send query, in which is processed specially.
     * @param stting $sql                  The query that will be prepared
     * @param array $bind                  Types, and params to be binded before execute
     * @param bool $rr                     Return rows (Array), or returns SQLObject (Object).
     * @return boolean
     */
    public function query($sql, $bind, $rr = false)
    {
        if(!$this->isConnected()) throw new \Exception("Can't do it partner. Your database connection is not open.");
        try {
            if ($prep = self::$con->prepare($sql)) {
                if (isset($bind) && $bind != NULL) {
                    $out = array($bind[0]);
                    
                     foreach ($bind as $key => $value) {
                         if ($key != 0) {
                        $out[$key] = &$bind[$key];
                         }
                     }
                    
                    call_user_func_array(array($prep, "bind_param" ), $out);
                }
                if ($prep->execute()) {
                    $result = new SQLResponce($prep);
                    if(!$this->isInDefault) $this->set_default_db(); // reset default database
                    if ($rr) return ($result->hasRows()) ? $result->getData() : array();
                    return $result;
                }
            }
            throw new \Exception($this->getLastError());
        }
        catch (\Exception $ex) 
        { 
            if(!$this->isInDefault) $this->set_default_db(); // reset default database
            die("Error " . $ex->getMessage()); 
        }
    }
}
#endbuild
