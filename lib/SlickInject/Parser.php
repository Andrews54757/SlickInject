<?php

/*
\| @Author: LegitSoulja
\| @License: MIT
\| @Source: https://github.com/LegitSoulja/SlickInject
*/

namespace SlickInject;

/**
 * Class Parser
 * This class should not be extended. They are static functions, but grouped in an organized maner,
 * Most of this doc is self explanatory if you look @ SlickInject.php
 */
#buildbelow
class Parser
{
    
    /**
     * Reserved keywords to prevent future errors
     */
    private static $RESERVED_KEYWORDS = array("ADD", "KEYS", "EXTERNAL", "PROCEDURE", "ALL", "FETCH", "PUBLIC", "ALTER", "FILE", "RAISERROR", "AND", "FILLFACTOR", "READ", "ANY", "FOR", "READTEXT", "AS", "FOREIGN", "RECONFIGURE", "ASC", "FREETEXT", "REFERENCES", "AUTHORIZATION", "FREETEXTTABLE", "REPLICATION", "BACKUP", "FROM", "RESTORE", "BEGIN", "FULL", "RESTRICT", "BETWEEN", "FUNCTION", "RETURN", "BREAK", "GOTO", "REVERT", "BROWSE", "GRANT", "REVOKE", "BULK", "GROUP", "RIGHT", "BY", "HAVING", "ROLLBACK", "CASCADE", "HOLDLOCK", "ROWCOUNT", "CASE", "IDENTITY", "ROWGUIDCOL", "CHECK", "IDENTITY_INSERT", "RULE", "CHECKPOINT", "IDENTITYCOL", "SAVE", "CLOSE", "IF", "SCHEMA", "CLUSTERED", "IN", "SECURITYAUDIT", "COALESCE", "INDEX", "SELECT", "COLLATE", "INNER", "SEMANTICKEYPHRASETABLE", "COLUMN", "INSERT", "SEMANTICSIMILARITYDETAILSTABLE", "COMMIT", "INTERSECT", "SEMANTICSIMILARITYTABLE", "COMPUTE", "INTO", "SESSION_USER", "CONSTRAINT", "IS", "SET", "CONTAINS", "JOIN", "SETUSER", "CONTAINSTABLE", "KEY", "SHUTDOWN", "CONTINUE", "KILL", "SOME", "CONVERT", "LEFT", "STATISTICS", "CREATE", "LIKE", "SYSTEM_USER", "CROSS", "LINENO", "TABLE", "CURRENT", "LOAD", "TABLESAMPLE", "CURRENT_DATE", "MERGE", "TEXTSIZE", "CURRENT_TIME", "NATIONAL", "THEN", "CURRENT_TIMESTAMP", "NOCHECK", "TO", "CURRENT_USER", "NONCLUSTERED", "TOP", "CURSOR", "NOT", "TRAN", "DATABASE", "NULL", "TRANSACTION", "DBCC", "NULLIF", "TRIGGER", "DEALLOCATE", "OF", "TRUNCATE", "DECLARE", "OFF", "TRY_CONVERT", "DEFAULT", "OFFSETS", "TSEQUAL", "DELETE", "ON", "UNION", "DENY", "OPEN", "UNIQUE", "DESC", "OPENDATASOURCE", "UNPIVOT", "DISK", "OPENQUERY", "UPDATE", "DISTINCT", "OPENROWSET", "UPDATETEXT", "DISTRIBUTED", "OPENXML", "USE", "DOUBLE", "OPTION", "USER", "DROP", "OR", "VALUES", "DUMP", "ORDER", "VARYING", "ELSE", "OUTER", "VIEW", "END", "OVER", "WAITFOR", "ERRLVL", "PERCENT", "WHEN", "ESCAPE", "PIVOT", "WHERE", "EXCEPT", "PLAN", "WHILE", "EXEC", "PRECISION", "WITH", "EXECUTE", "PRIMARY", "WITHIN", "GROUP", "EXISTS", "PRINT", "WRITETEXT", "EXIT", "PROC");
    
    final static private function WHERE($arr, $required = false)
    {
        $append = $values = array();
        $flag   = 0;
        foreach ($arr as $k => $v) {
            if (!is_numeric($k) && !empty($v)) {
                if (in_array(strtoupper($k), self::$RESERVED_KEYWORDS)): array_push($append, "`" . $k . "`=?");
                else: array_push($append, "" . $k . "=?"); endif;
                array_push($append, 'AND');
                array_push($values, $v);
                $flag = 1;
            } else {
                if ($flag === 0 && $required === TRUE) throw new \Exception("An error has occured");
                if ($flag === 1) array_pop($append);
                array_push($append, $v);
                $flag = 2;
            }
        }
        
        if ($flag === 1) 
        { array_pop($append); }
        
        $types = "";
        foreach ($values as $v) 
        { $types .= self::getType($v); }
        
        if (!empty($types)) array_unshift($values, $types);
        return array( $append, $values );
    }
    
    /**
     * Get initial of type in which is needed to bind the proper types when executing to the database
     * @param string $type               <T>
     * @return char
     */
    final static private function getType($type)
    {
        switch (gettype($type)) {
            case "string": return "s";
            case "boolean": // bool is recognized as an integer
            case "integer": return "i";
            case "double": return "d";
            default: throw new \Error("Unable to bind params");
        }
    }
    
    final static public function SELECT($columns, $table, $where, $explain = false)
    {
        $columns = (count($columns) > 0) ? $columns : array("*");
        
        foreach ($columns as $k => $v) {
            if (in_array(strtoupper($v), self::$RESERVED_KEYWORDS)) 
                $columns[$k] = "`" . $v . "`";
        }
        
        $where = (count($where) > 0) ? self::WHERE($where) : NULL;
        $sql   = (($explain) ? "EXPLAIN " : "");
        
        if (in_array(strtoupper($table), self::$RESERVED_KEYWORDS)) 
        { $table = "`" . $table . "`"; }
        
        // $sql .= "SELECT [" . join(", ", $columns) . "] FROM " . $table;
        $sql .= "SELECT " . join(", ", $columns) . " FROM " . $table;
        
        if ($where != NULL && count($where[1]) > 1 && isset($where[0])) 
        { $sql .= " WHERE " . join(" ", $where[0]); } 
        elseif (isset($where[0])) 
        { $sql .= " " . join(" ", $where[0]); }
        
        return array( $sql, (isset($where[1])) ? $where[1] : NULL );
    }
    final static public function INSERT($table, $object)
    {
        if (in_array(strtoupper($table), self::$RESERVED_KEYWORDS)) 
        { $table = "`" . $table . "`"; }
        $sql     = "INSERT INTO " . $table;
        $names   = array();
        $replace = array();
        $values  = array();
        foreach ($object as $k => $v) {
            if (isset($k) && isset($v)) {
                if (is_numeric($k)) return;
                array_push($names, "`" . $k . "`");
                array_push($replace, "?");
                array_push($values, $v);
            }
        }
        $sql .= " (" . join(", ", $names) . ") VALUES ";
        $sql .= " (" . join(", ", $replace) . ")";
        
        $types = "";
        foreach ($values as $v) 
        { $types .= self::getType($v); }
        
        array_unshift($values, $types);
        
        return array( $sql, $values );
    }
    final static public function UPDATE($table, $object, $where)
    {
        if (in_array(strtoupper($table), self::$RESERVED_KEYWORDS)) 
        { $table = "`" . $table . "`"; }
        
        $insert = array();
        $values = array();
        $where  = (count($where) > 0) ? self::WHERE($where, TRUE) : NULL;
        $sql    = "UPDATE " . $table . " SET";
        foreach ($object as $k => $v) {
            if (isset($k) && isset($v)) {
                if (is_numeric($k)) continue;
                if (in_array(strtoupper($k), self::$RESERVED_KEYWORDS)): array_push($insert, "`" . $k . "`=?");
                else: array_push($insert, "" . $k . "=?"); endif;
                array_push($values, $v);
            }
        }
        $sql .= " " . join(", ", $insert);
        if ($where != NULL) 
        { $sql .= " WHERE " . join(" ", $where[0]); }
        
        $types = "";
        
        foreach ($values as $v) 
        { $types .= self::getType($v); }
        
        if ($where != NULL) {
            $types .= $where[1][0];
            array_shift($where[1]);
        }
        
        $ni = count($values);
        
        foreach ($where[1] as $k => $v) {
            $values[$ni] = $v;
            $ni++;
        }
    
        
        array_unshift($values, $types);
        return array( $sql, $values );
    }
    final static public function TRUNCATE($table)
    {
        $sql = "TRUNCATE TABLE `" . $table . "`";
        return array( $sql );
    }
    final static public function DELETE($table, $where)
    {
        $sql = "DELETE FROM `" . $table . "`";
        if (count($where) > 0) {
            $where = self::WHERE($where);
            $sql .= " WHERE " . join(" ", $where[0]);
        }
        return array( $sql, $where[1] );
    }
}
#endbuild
