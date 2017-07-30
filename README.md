### SlickInject!

###### ::v2

#### Supports PHP (v5.6 - v7.1)
- [x] MySQLi (100% fully supported)
> Extensions needed
- - mysqlnd/nd_mysqli

> ###### [![g](https://img.shields.io/badge/build-passing-brightgreen.svg)](#) [![GitHub issues](https://img.shields.io/github/issues/LegitSoulja/SlickInject.svg)](https://github.com/LegitSoulja/SlickInject/issues) [![GitHub forks](https://img.shields.io/github/forks/LegitSoulja/SlickInject.svg)](https://github.com/LegitSoulja/SlickInject/network) [![GitHub stars](https://img.shields.io/github/stars/LegitSoulja/SlickInject.svg)](https://github.com/LegitSoulja/SlickInject/stargazers)

SlickInject is a PHP library in which allows you to write efficent code, and be safe and secure while doing so. Using this library, makes ease of writing code for your database easier, faster, and no need of extra work/worries of having your database hacked upon an injection, or some random errors. SlickInject does this all for you, very simple.

Using SlickInject, just remember how your SQL is wrote. You'll slowly start to understand the concept behind this, and hopefully you may even have a way to make this even better.

#### Installation
I have prepared/compiled SlickInject into 1 file if you're interested. It's inside the build folder, or [Download/Generate a new build](http://legitsoulja.info/SlickInject.php). However, Load all the files in lib folder. Composer should load, or manually load the 3 files. test.php also provides a library loader I wrote.

SlickInject is still being debugged, and constantly updated. This documentation is not 100% well complete and documented. 


#### Connect to database.

> When connecting to the databse, it requires 4 arguments. Obviously your database credentials, including your database name. Instantiating this object without arguments is safe, as you can connect at any time using the connect method providing your database credentials.

```php
$si = new SlickInject("host", "username", "password", "database_name");
```

### SELECT

> Using SELECT, you will automatically get returned the selected rows in an array format. Check examples for more detailed information. 

##### Notes about ```SELECT```

- **The 1st argument ```must``` be an array. ```[]``` = ```*```**
- **The 3rd argument ```must``` be an array, and or ```NULL```**
- **The 4th argument is ```true``` by default. Setting it ```false``` will return ```SlickInject\SQLResponce``` (see below).**


The example query correlates to the code example of the way you should think when writing.

###### ```SELECT * FROM `table` ```

```php
$si->SELECT([], "table", []); // [] = *, 1st argument (All rows)
```



###### ```SELECT * FROM `table` WHERE `id` = 1```

#### Notes about ```WHERE```
- **The 3rd argument as spoke, is WHERE in this case. **

```php

// @param array []       |  Columns you're receiving
// @param string "table" |  The table name
// @param array array()  |  WHERE cause

$si->SELECT([], "table", array("id"=>1));
```



###### ```SELECT * FROM `table` WHERE `id`=1 AND `group_id`=1```

```php
$si->SELECT([], "table", array("id"=>1, "group_id"=>1));

// - or - 

$si->SELECT([], "table", array("id"=>1, "AND", "group_id"=>1));
```

###### ```SELECT `email` FROM `table` ORDER BY id```

```php
$si->SELECT(["email"], "table", array("ORDER BY", "id"))
```

###### ```SELECT `email` FROM `table` WHERE id = 1 ORDER BY id```

```php
$si->SELECT(["email"], "table", array("id"=>1, "ORDER BY", "id"))
```



###### ```SELECT `email` FROM `table` WHERE `group_id`=1 AND `id` > 1```

```php
$si->SELECT(["email"], "table", array("groud_id"=>1, "`id`>1"));
```

##### Change database

```php
$si->select_db("otherdatabase")->SELECT([], "table"); 
```

###### SQLResponce | SELECT (Example)

Except for the ```SELECT``` method, the others will return you ```SQLResponce```, however by default, you are given the rows of the selected data as an array, which can be toggled false as the 4th argument when using ```SELECT```

###### Obtain number of rows

```php
$response = $si->SELECT([], "table", [], false);

print_r($response ->num_rows()); // int
```



### UPDATE

###### ```UPDATE `table` SET `username`="Guest" WHERE `id`=1```

```php

$username = "Guest";

$si->UPDATE('users', array("username"=>$username), array("id"=>1));
```



### INSERT



###### ```INSERT INTO `table` (`username`, `email`) VALUES ('Johnny', 'test@email.com')```

```php

$username = "Johnny";

$email = "example@example.com"

// very simple, and easy.

$si->INSERT('table', array("username"=>$username, "email"=>$email));

```



### DELETE

###### ```DELETE FROM `table` WHERE `id`=1```

```php

$si->DELETE("table", array("id"=>1));

```



### TRUNCATE



###### ```TRUNCATE TABLE `table` ```

```php

$si->TRUNCATE("table");

```



### Closing Database

When using SlickInject, it does not handle rather or not the connection should be open or closed. Using the statement below, you can close your databse when you're done using it.

```php

$si->close();

```

### SQLResponce



#### Functions | Return <T> | Description

> Last arguments for SlickInject must be false as of [this](https://github.com/LegitSoulja/SlickInject/blob/dev/README.md#obtain-mysqli_query-request), to get SQLResponce 

- hasRows() :: bool : If query returned any rows

- getResult() :: object : Returns mysqli_query responce

- num_rows() :: integer : Return number of rows

- getData() :: array : Return query table row(s)

- didAffect() :: bool : If any rows was affected during execution

- error() :: bool : If result doesn't exist or contain any errors



### SQLObject

Sometimes, SlickInject may lack certain things when managing your database. You can use SQLObject, as a below which is returned by SlickInject.

```php
// $si = new SlickInject(connected to databse)
$sqlobject = $si->getSQLObject(); // store object

// execute freely
$sql->query("SELECT * FROM `table`", NULL, true); // return rows

```





### SQLObject

SQLObject can be used stand-alone if you don't like the quick and ease of use with SlickInject. Maybe, it's missing something. SQLObject Documentation..


###### Connecting

- Method 1

```php
$sql = new \SlickInject\SQLObject("localhost", "username", "password", "database");
```

- Method 2

```php
$sql = new \SlickInject\SQLObject();
$sql->connect("localhost", "username", "password", "database");
```



###### Sending Queries

```php
$sql->query("INSERT INTO table (username) VALUES ('LegitSoulja')", NULL); // *SQLResponce
//

$sql->query("SELECT * FROM table", NULL, true); // Array : Get array of requested table rows
```



###### Ping

```php
$sql->ping(); // Boolean : Returns if database is still connected
```



###### Errors

```php
$sql->getConnectionError(); // String : Get connect errror, if present

//

$sql->getLastError(); // String : Get last error from last executed query
```



###### Close Datbase

```php

$sql->close(); 

```





#### Functions | Return <T> | Description

- connect(s,s,s,s) :: void : Connect to databse.

- getConnectionError() :: integer : Get database connection status error code

- getLastError() :: string : Get last database error

- escapeString(s) :: string :: **Deprecated**

- ping() :: bool : Return if connection is still live and not closed.

- query() :: void : Shouldn't be used unless you know what you're doing.






### Future Additions


- [ ] Create "Tables, Databases" with ease.

- [ ] Complex/Smart algorithm of building SQL queries w/ parser.

- [ ] Create a parser, in which validates your SQL queries, and validate queries before executed (Prevent SQL Injection).

- [ ] More in dept usage/mech/doc/additions of SlickInject (Plan, and make methods/functions understandable, easier to understand/learn/use).


## Contribute



> If you see a vulnerability, or recommend an extra security layer, or just want to help in any-way, you are allowed, and welcomed. This project may/may not be (updated/maintained), therefore, it is in your hands to maintain. 



However, SlickInject is also useful for those who are new to working with databases(SQL), or php even. This is a great start!. It has never been this easier, and safe to work with databases. I am now very happy to build sites more comfortable with the power of SlickInject. 

