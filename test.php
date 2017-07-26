<?php
function _load_all($a) {
  foreach (glob($a) as $b) :
    if (is_dir($b) && _load_all($b . "/*")) : continue; else:
    if ($b == __FILE__) : continue; endif; endif;
    if (pathinfo($b)['extension'] == "php") : include $b; endif;
  endforeach;
}
_load_all("lib/*");


// Connect to the database / create instance
$si = new SlickInject("localhost", "username", "password", "database_name");
// $si->connect() is usable aswell, instead of passing the args via the constructor

// Get rows from database as an array
$data = $si->SELECT([], "table", array("id"=>1)); // @Array : Returns array
print_r($data); // print data

// INSERT :: INSERT INTO `table` (`id`,`username`) VALUES (5, 'bob')
$si->INSERT('table', array("id"=>5,"username"=>"bob")); // @SQLResponce

// UPDATE :: UPDATE `table` SET `username`='bobo' WHERE `id`=5
$si->UPDATE('table', array("username"=>"bobo"), array("id"=>5)); // @SQLResponce

// DELETE :: DELETE FROM `table` WHERE `username`='bobo'
$si->DELETE('table', array("username"=>"bobo")); // @SQLResponce

// TRUNCATE
$si->TRUNCATE("table"); // @SQLResponce

// Advanced Selecting
// :: SELECT id, username, email FROM `table` WHERE id > 5 AND active = 1
$si->SELECT(["id", "username", "email"], "table", array("id > 5", "AND", "active = 1")); // @array

// Reserved Keyword Handler : Mistakes happens
// :: SELECT `from`, `where`, `key` FROM `table`
$si->SELECT(["from", "where", "key"], "table"); // @array

// change database name
$si->select_db("otherdatabase")->SELECT([], "table");
// to continue using this database, you will need to select it again.

// SQLResponce Example
$responce = $si->SELECT([], "table", [], false); // @SQLResponce

if($responce->hasRows()) {
    $number_of_rows = $responce->num_rows();
    $rows = $responce->getData();
    foreach($rows as $row) {
        // iterate
    }
}

// CLOSE : ALWAYS CLOSE YOUR DATABASE IF NOT LONGER BEING USED
$si->close();

